<?php
namespace App\Controllers;

class AboutController extends \Controller {
    public function index($slug = null) {
        $data = [
            'title' => 'Biz Haqimizda',
            'slug' => $slug,
            'content' => 'Bu bizning kompaniyamiz haqida ma\'lumot.'
        ];
        return $this->view('about', $data);
    }

    public function team() {
        return $this->view('about.team');
    }

    public function contact() {
        return $this->view('about.contact');
    }
}