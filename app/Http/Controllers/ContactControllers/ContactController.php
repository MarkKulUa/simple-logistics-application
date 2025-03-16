<?php

namespace App\Http\Controllers\ContactControllers;

use App\Models\Contact;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\StoreContactRequest;
use App\Http\Requests\Contact\UpdateContactRequest;
use App\Jobs\SyncGoogleContactJob;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        return response()->json(Contact::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(StoreContactRequest $request)
    {
        $contact = Contact::create($request->validated());

        if ($contact) {
            SyncGoogleContactJob::dispatch($contact->email, 'created', $contact->getGoogleSyncData());

            return response()->json([
                'message' => trans('contacts.createSuccess'),
                'contact' => $contact,
            ]);
        }

        return response()->json([
            'message' => trans('contacts.createError'),
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateContactRequest $request, $id)
    {
        // Get the contact
        $contact = Contact::find($request->contact_id);

        $originalEmail = $contact->email;

        if ($contact->update($request->validated())) {

            SyncGoogleContactJob::dispatch($contact->email, 'updated', $contact->getGoogleSyncData(), $originalEmail);

            return response()->json([
                'message' => trans('contacts.editSuccess'),
                'contact' => $contact,
            ]);
        }

        return response()->json([
            'message' => trans('contacts.editError'),
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $contact = Contact::findOrFail($id);

        if ($contact->delete()) {
            SyncGoogleContactJob::dispatch($contact->email, 'deleted');

            return response()->json([
                'message' => trans('contacts.deleteSuccess'),
            ]);
        }

        return response()->json([
            'message' => trans('contacts.deleteError'),
        ], 404);
    }

    /**
     * Get name by id.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getName($id)
    {
        $contact = Contact::findOrFail($id);

        return response()->json(['name' => $contact->getName()]);
    }
}
