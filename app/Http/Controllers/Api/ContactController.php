<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NouveauContact;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom'        => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'telephone'  => 'nullable|string|max:20',
            'entreprise' => 'nullable|string|max:255',
            'sujet'      => 'required|string|max:255',
            'message'    => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $contact = Contact::create([
            'reference'  => Contact::genererReference(),
            'nom'        => $request->nom,
            'email'      => $request->email,
            'telephone'  => $request->telephone,
            'entreprise' => $request->entreprise,
            'sujet'      => $request->sujet,
            'message'    => $request->message,
            'statut'     => 'nouveau',
        ]);

        // Notifier l'admin par email
        try {
            $adminEmail = \App\Models\Parametre::get('site_email', 'liferopro@gmail.com');
            Mail::to($adminEmail)->send(new NouveauContact($contact));
        } catch (\Exception $e) {
            Log::warning("Echec envoi email contact {$contact->reference}: {$e->getMessage()}");
        }

        return response()->json([
            'success'   => true,
            'reference' => $contact->reference,
            'message'   => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les 24h.',
        ], 201);
    }
}
