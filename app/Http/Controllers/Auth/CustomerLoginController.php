<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerLoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->authenticatedRedirect();
        }

        return view('content.authentications.auth-login-basic');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return $this->authenticatedRedirect();
        }

        $previousSessionId = $request->session()->getId();
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak valid.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('cart_session_id_before_login', $previousSessionId);

        if (Auth::user()->role !== 'customer') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Gunakan halaman login admin untuk akun administrator.'])
                ->onlyInput('email');
        }

        return redirect()->intended(route('home'))->with('status', 'Login berhasil.');
    }

    private function authenticatedRedirect(): RedirectResponse
    {
        return redirect()->route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'home');
    }
}
