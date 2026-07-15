<?php
namespace App\Controllers;

use Qadamchi\Http\Controller;
use Qadamchi\Auth\Auth;

/**
 * Asosiy sahifa controller'i.
 * Laravel'da bunga WelcomeController yoki HomeController to'g'ri keladi.
 */
class WelcomeController extends Controller
{
    /** Bosh sahifa — Laravel'ning welcome sahifasiga to'g'ri keladi. */
    public function index()
    {
        return view('welcome');
    }

    /** Auth bilan himoyalangan dashboard (auth middleware orqali). */
    public function dashboard()
    {
        return view('pages.dashboard', ['user' => Auth::user()]);
    }
}