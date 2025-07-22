<?php
namespace App\Requests;

use Validator;

class CreateUserRequest {
    public static function rules() {
        return [
            'name' => 'required|min:2|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ];
    }
    
    public static function messages() {
        return [
            'name.required' => 'Ism majburiy maydon.',
            'name.min' => 'Ism kamida 2 ta belgi bo\'lishi kerak.',
            'email.required' => 'Email manzil majburiy.',
            'email.email' => 'To\'g\'ri email manzil kiriting.',
            'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan.',
            'password.required' => 'Parol majburiy.',
            'password.min' => 'Parol kamida 8 ta belgi bo\'lishi kerak.',
            'password.confirmed' => 'Parol tasdiqlash mos kelmaydi.'
        ];
    }
    
    public static function validate($data) {
        $validator = Validator::make($data, static::rules(), static::messages());
        
        if ($validator->fails()) {
            return $validator->errors();
        }
        
        return true;
    }
    
    public static function authorize() {
        return true; // Hamma ruxsat
    }
}