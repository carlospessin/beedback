<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role']['value'],
            'password' => Hash::make('12345678'), 
        ]);

        return redirect()->back()->with('success', 'Usuário criado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role' => 'required',
        ]);
        
        try {
            $user = User::findOrFail($id);

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role_id' => $validated['role'],
            ]);
            
            return redirect()->back()->with('success', 'Usuário criado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('data', 'Erro ao atualizar usuário', 400);
        }
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
