<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_rrule_templates', function (Blueprint $table) {
            $table->id();
            $table->string('rrule', 500)->comment('Recurrence rule frequency based on iCal standard');
            $table->string('parity', 4)->nullable()->comment('Parity type: add, even or null');
            $table->decimal('service_ratio', 8, 2)->default(0.40);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });

        // Insert predefined rrule templates
        $rrules = [
            'FREQ=WEEKLY;INTERVAL=1;BYDAY=MO',
            'FREQ=WEEKLY;BYDAY=FR',
            'FREQ=WEEKLY;BYDAY=FR,TU',
            'FREQ=WEEKLY;BYDAY=MO',
            'FREQ=WEEKLY;BYDAY=MO,FR',
            'FREQ=WEEKLY;BYDAY=MO,TH',
            'FREQ=WEEKLY;BYDAY=MO,WE',
            'FREQ=WEEKLY;BYDAY=MO,WE,FR',
            'FREQ=WEEKLY;BYDAY=SA',
            'FREQ=WEEKLY;BYDAY=SU',
            'FREQ=WEEKLY;BYDAY=TH',
            'FREQ=WEEKLY;BYDAY=TU',
            'FREQ=WEEKLY;BYDAY=WE',
            'FREQ=WEEKLY;INTERVAL=1;BYDAY=MO,TU,WE,TH,FR,SA,SU',
            'FREQ=WEEKLY;INTERVAL=2;BYDAY=MO',
            'FREQ=MONTHLY;BYDAY=FR;BYSETPOS=1',
            'FREQ=MONTHLY;INTERVAL=3;BYDAY=MO,TU,WE,TH,FR,SA,SU',
            'FREQ=YEARLY;INTERVAL=1;BYDAY=MO;BYMONTHDAY=1;BYMONTH=1',
            'FREQ=YEARLY;INTERVAL=1;BYDAY=MO'
        ];

        foreach ($rrules as $rrule) {
            DB::table('service_rrule_templates')->insert([
                'rrule' => $rrule,
                'parity' => null,
                'service_ratio' => 0.40,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_rrule_templates');
    }
};
