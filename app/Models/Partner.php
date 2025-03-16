<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use SoftDeletes, Prunable, HasFactory;

    protected $fillable = [
        'user_id', 'trainer_id', 'ending_reason_tag_id', 'first_name', 'last_name',
        'company_name', 'company_number', 'email', 'phone', 'photo_location',
        'billing_address_city', 'billing_address_street', 'billing_address_postal_code',
        'contract_signed', 'contract_ended', 'is_insured', 'insurance_expiration_date',
        'account_prefix', 'account_number', 'account_bank_code', 'suite_guid',
        'account_iban', 'account_swift', 'account_bank_owner', 'vat_number',
        'guaranteed_earnings', 'is_trainer', 'training_day_date', 'do_not_contact',
        'notes', 'ending_reason', 'ending_note', 'crm_partner_id', 'partner_type',
        'country_of_birth', 'birth_city', 'birth_date'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'contract_signed' => 'date:Y-m-d',
        'contract_ended' => 'date:Y-m-d',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['currency_iso_4217', 'country_code_iso_3166'];

    public function toSearchableArray()
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name,
            'company_number' => $this->company_number,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    /**
     * Get the user that created the partner.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Get the ending tag of the partner.
     */
    public function endingTag()
    {
        return $this->hasOne(PartnerEndingReasonTag::class, 'id');
    }

    /**
     * Check if the partner is active.
     */
    public function isActive(DateTime $dateFrom, DateTime $dateTo)
    {
        $hasCleanings =
            $this->cleanings()
                ->whereBetween('date_expected', [$dateFrom, $dateTo])
                ->count() > 0;
        $hasSubstitutions =
            $this->substitutions()
                ->whereBetween('date_expected', [$dateFrom, $dateTo])
                ->count() > 0;

        return $hasSubstitutions || $hasCleanings;
    }

    /**
     * Scope a query to only include active partners.
     */
    public function scopeActive(Builder $query)
    {
        $query->where('contract_ended', '>=', now())->orWhereNull('contract_ended');
    }

    /**
     * Scope a query to only include active partners.
     */
    public function scopeNotActive(Builder $query)
    {
        $query->where('contract_ended', '<', now())->whereNotNull('contract_ended');
    }

    /**
     * Get the prunable model query.
     */
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subMonths(3));
    }

    /**
     * Get currency ISO 4217 of the partner's city branches.
     */
    public function getCurrencyIso4217Attribute()
    {
        return 'USD';
    }

    /**
     * Get currency ISO 3166 of the partner's city branches.
     */
    public function getCountryCodeIso3166Attribute()
    {
        return 'USD';
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        parent::booted();

        /**
         * When user isn't specified get the authenticated
         */
        static::creating(function ($partner) {
            if (empty($partner->user_id)) {
                $partner->user_id = auth('api')->user()->id;
            }
        });
    }

    public function getGoogleSyncData(): array
    {
        $givenName = trim($this->first_name ?: '-');
        $familyName = trim($this->last_name ?: '-');
        $email = trim($this->email);

        return [
            'givenName'  => "{$givenName}",
            'familyName' => "{$familyName}",
            'email'      => "{$email}",
        ];
    }

    public function getBankAccount()
    {
        if ($this->currency_iso_4217 && $this->currency_iso_4217 === "EUR" && $this->account_iban) {
            return $this->account_iban;
        }

        if ($this->account_number) {
            return $this->account_prefix
                ? "{$this->account_prefix}-{$this->account_number}/{$this->account_bank_code}"
                : "{$this->account_number}/{$this->account_bank_code}";
        }

        return '';
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
