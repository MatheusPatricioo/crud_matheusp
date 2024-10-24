<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Certifique-se de que isso está importado
use Illuminate\Support\Facades\Validator; // Para a validação

class AdminController extends Controller
{
    public function login(Request $request)
    {
        // Se o método for GET, exibe a view do formulário de login
        if ($request->isMethod('get')) {
            return view('admin/login');
        }

        // Regras de validação
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:7'
        ];

        // Validação dos dados
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Se a validação falhar, retorna os erros para a view
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Tentativa de login
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Se autenticar com sucesso, redireciona para a página /admin
            return redirect('/admin');
        }

        // Se falhar na autenticação, retorna com erro
        return redirect()->back()->with('error', 'Credenciais inválidas.');
    }

    public function loginAction(Request $request)
    {
        $creds = $request->only('email', 'password');

        // Tentativa de autenticação
        if (Auth::attempt($creds)) {
            return redirect('/admin');
        } else {
            // Adiciona a mensagem de erro à sessão
            return redirect('/admin/login')->with('error', 'E-mail e/ou senha não conferem.');
        }
    }


    public function register()
    {
        echo 'cadastro...';
    }
}
