<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Page;


class AdminController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin/login');
        }

        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:7'
        ];

        $messages = [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 7 caracteres.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/admin');
        }

        return redirect()->back()->with('error', 'E-mail e/ou senha não conferem.');
    }

    public function loginAction(Request $request)
    {
        $creds = $request->only('email', 'password');

        if (Auth::attempt($creds)) {
            return redirect('/admin');
        } else {
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

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        Auth::login($user);

        return redirect('/admin');
    }

    public function registerAction(Request $request)
    {
        $creds = $request->only('email', 'password', 'name');

        $hasEmail = User::where('email', $creds['email'])->count();

        if ($hasEmail === 0) {
            $newUser = new User();
            $newUser->name = $creds['name'];
            $newUser->email = $creds['email'];
            $newUser->password = Hash::make($creds['password']);
            $newUser->save();

            Auth::login($newUser);

            return redirect('/admin')->with('success', 'Usuário cadastrado com sucesso!');
        } else {
            return redirect('/admin/register')->with('error', 'Já existe um usuário com este e-mail.');
        }
    }
    public function index()
    {
        $user = Auth::user();

        $pages = Page::where('id_user', $user->id)->get();

        // $title = 'Dashboard Admin';
        // $bg = '#f4f4f4'; // Exemplo de cor de fundo
        // $font_color = '#333'; // Exemplo de cor da fonte
        // $profile_image = 'path/to/profile-image.jpg'; // Substitua pelo caminho da imagem de perfil
        // $description = 'Bem-vindo ao painel de pipipi popopo';
        // $links = [
        //     (object) ['url' => 'https://example.com', 'title' => 'Exemplo 1'],
        //     (object) ['url' => 'https://another-example.com', 'title' => 'Exemplo 2']
        // ];

        // return view('admin.index',
        //  compact('title', 'bg', 'font_color', 'profile_image', 'description', 'links'));
        return view('admin.index', [
            'pages' => $pages
        ]);
    }

    public function logout () {
        Auth::logout();
        return redirect ('/admin');
    }

}
