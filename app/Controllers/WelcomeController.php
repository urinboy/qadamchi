<?php
namespace App\Controllers;

class WelcomeController extends \Controller {
    public function index() {
        $this->view('welcome');
    }
}