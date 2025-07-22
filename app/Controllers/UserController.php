<?php
// Controller'da foydalanish:
namespace App\Controllers;

use App\Models\User;
use App\Requests\CreateUserRequest as RequestsCreateUserRequest;
use Controller;

class UserController extends Controller {
    public function store() {
        $data = $_POST;
        
        // Validation
        $validation = RequestsCreateUserRequest::validate($data);
        
        if ($validation !== true) {
            // Xato bo'lsa
            $this->view('users/create', ['errors' => $validation]);
            return;
        }
        
        if (!RequestsCreateUserRequest::authorize()) {
            http_response_code(403);
            echo "Ruxsat yo'q";
            return;
        }
        
        // Ma'lumotlar to'g'ri bo'lsa, user yaratish
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        
        $this->redirect('/users');
    }
}