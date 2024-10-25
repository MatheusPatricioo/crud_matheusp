<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Certifique-se de que isso está importado
use Illuminate\Support\Facades\Validator; // Para a validação
use App\Models\User; // Importa o modelo de usuário
use Illuminate\Support\Facades\Hash;

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

        // Mensagens personalizadas de validação
        $messages = [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 7 caracteres.'
        ];

        // Validação dos dados
        $validator = Validator::make($request->all(), $rules, $messages);

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
        return redirect()->back()->with('error', 'E-mail e/ou senha não conferem.');
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
    public function register(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin/register');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:7|confirmed'
        ];

        $messages = [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 7 caracteres.',
            'password.confirmed' => 'As senhas não conferem.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cria o novo usuário
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        // Login automático após cadastro
        Auth::login($user);

        return redirect('/admin');
    }

    public function registerAction(Request $request)
    {
        $creds = $request->only('email', 'password', 'name');

        // Verifica se o e-mail já está em uso
        $hasEmail = User::where('email', $creds['email'])->count();

        if ($hasEmail === 0) {
            $newUser = new User();
            $newUser->name = $creds['name'];
            $newUser->email = $creds['email'];
            $newUser->password = Hash::make($creds['password']); // Usando Hash::make para segurança
            $newUser->save();

            return redirect('/admin')->with('success', 'Usuário cadastrado com sucesso!');
        } else {
            return redirect('/admin/register')->with('error', 'Já existe um usuário com este e-mail.');
        }
    }

}
