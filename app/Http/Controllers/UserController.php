<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Buscando todos os usuários no banco de dados
        $users = User::all();
        $authenticatedUser = auth()->user();

        // Retornando a view com os dados dos usuários
        return Inertia::render('Usuarios', [
            'users' => $users,
            'role_id' => $authenticatedUser->role_id, 
        ]);
    }

    public function store(Request $request)
    {
        // Validação dos dados recebidos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'role.value' => 'required', 
        ]);

        // Criar o novo usuário no banco de dados
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role']['value'],
            'password' => Hash::make('12345678'), 
        ]);

        return redirect()->back()->with('success', 'Usuário criado com sucesso!');
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            
            return response()->json(['message' => 'Usuário excluído com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao excluir usuário.'], 400);
        }
    }
}
