<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    /** Liste tous les administrateurs */
    public function index(): JsonResponse
    {
        $admins = User::where('role', 'admin')
            ->select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $admins,
        ]);
    }

    /** Créer un nouvel administrateur */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required'     => 'Le nom est obligatoire.',
            'email.required'    => 'L\'email est obligatoire.',
            'email.unique'      => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed'=> 'Les mots de passe ne correspondent pas.',
            'password.min'      => 'Le mot de passe doit faire au moins 8 caractères.',
        ]);

        $admin = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Administrateur créé avec succès.',
            'data'    => [
                'id'    => $admin->id,
                'name'  => $admin->name,
                'email' => $admin->email,
                'role'  => $admin->role,
            ],
        ], 201);
    }

    /** Supprimer un administrateur */
    public function destroy(int $id): JsonResponse
    {
        // Empêcher de se supprimer soi-même
        if (auth()->id() === $id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ], 403);
        }

        $admin = User::where('id', $id)->where('role', 'admin')->firstOrFail();
        $admin->tokens()->delete(); // Révoquer tous les tokens
        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Administrateur supprimé.',
        ]);
    }
}
