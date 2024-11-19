<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        // Buscando todos os usuários no banco de dados
        $users = User::all();

        // Retornando a view com os dados dos usuários
        return Inertia::render('Usuarios', [
            'users' => $users,
        ]);
    }
}
