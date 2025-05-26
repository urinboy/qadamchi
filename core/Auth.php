<?php
class Auth {
    public static function login($user) {
        Session::put('user', $user);
    }
    public static function user() {
        return Session::get('user');
    }
    public static function logout() {
        Session::put('user', null);
    }
    public static function check() {
        return Session::get('user') ? true : false;
    }
}