<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's allowed profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date'],
        ]);

        $user->update($dados);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Dados atualizados com sucesso.');
    }
}