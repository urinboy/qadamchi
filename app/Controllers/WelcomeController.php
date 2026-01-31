<?php
namespace App\Controllers;

class WelcomeController extends \Controller {
    public function index() {
        $data = ['title' => 'Qadamchi Framework'];
        return $this->view('welcome', $data, null); // Layout ishlatmaslik
    }
}