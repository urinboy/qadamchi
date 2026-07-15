<?php
namespace App\Requests;

use Qadamchi\Validation\FormRequest;

/**
 * Ro'yxatdan o'tish formasi uchun validatsiya (FormRequest).
 * Controller'da: $data = (new CreateUserRequest)->validate();
 */
class CreateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|min:2|max:50',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Ism majburiy.',
            'name.min'          => 'Ism kamida 2 ta belgi bo\'lishi kerak.',
            'email.required'    => 'Email majburiy.',
            'email.email'       => 'Email noto\'g\'ri formatda.',
            'email.unique'      => 'Bu email allaqachon ro\'yxatdan o\'tgan.',
            'password.required' => 'Parol majburiy.',
            'password.min'      => 'Parol kamida 8 ta belgi bo\'lishi kerak.',
            'password.confirmed'=> 'Parol tasdiqlash mos kelmadi.',
        ];
    }
}