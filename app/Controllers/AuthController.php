<?php
namespace App\Controllers;

use Qadamchi\Http\Controller;
use Qadamchi\Http\Request;
use Qadamchi\Http\Response;
use Qadamchi\Auth\Auth;
use App\Models\User;
use App\Requests\CreateUserRequest;

class AuthController extends Controller
{
    /** Ro'yxatdan o'tish formasi. */
    public function register()
    {
        return view('auth.register');
    }

    /** Ro'yxatdan o'tish — validatsiya + User yaratish. */
    public function store(CreateUserRequest $request)
    {
        $data = $request->validate();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        Auth::login($user);
        return redirect('/')->with('success', 'Ro\'yxatdan o\'tish muvaffaqiyatli! Xush kelibsiz, ' . $user->name . '.');
    }

    /** Kirish formasi. */
    public function login()
    {
        return view('auth.login');
    }

    /** Kirish — attempt. */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $name = Auth::user()->name ?? '';
            return redirect('/')->with('success', 'Xush kelibsiz' . ($name ? ', ' . $name : '') . '!');
        }

        return back()->with('error', 'Email yoki parol noto\'g\'ri.');
    }

    /** Chiqish. */
    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'Siz tizimdan muvaffaqiyatli chiqdingiz.');
    }
}