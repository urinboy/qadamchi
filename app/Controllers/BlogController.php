<?php
namespace App\Controllers;

class BlogController extends \Controller {
    public function index() {
        $data = ['title' => 'Blog'];
        return $this->view('blog.index', $data);
    }

    public function show($id) {
        $data = ['title' => 'Blog Post', 'id' => $id];
        return $this->view('blog.show', $data);
    }
}