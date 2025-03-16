<?php

namespace App\Http\Controllers\PartnerControllers;

use App\Http\Controllers\Controller;
use App\Jobs\SyncGoogleContactJob;
use App\Models\Partner;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        return response()->json(Partner::all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $partner = Partner::findOrFail($id);

        SyncGoogleContactJob::dispatch($partner->email, 'deleted');

        return response()->json(['success' => $partner->delete()]);
    }
}
