<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContactAdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Contact::latest();

        if ($request->statut) {
            $query->where('statut', $request->statut);
        }

        $contacts = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $contacts->items(),
            'meta'    => [
                'total'     => $contacts->total(),
                'last_page' => $contacts->lastPage(),
            ],
        ]);
    }

    public function updateStatut(Request $request, int $id): JsonResponse
    {
        $contact = Contact::findOrFail($id);
        $request->validate(['statut' => 'required|in:nouveau,lu,traite,archive']);

        $data = ['statut' => $request->statut];
        if ($request->statut === 'traite' && $request->reponse) {
            $data['reponse']     = $request->reponse;
            $data['repondu_at']  = now();
        }

        $contact->update($data);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour.', 'contact' => $contact]);
    }
}
