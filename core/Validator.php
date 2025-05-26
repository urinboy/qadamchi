<?php
class Validator {
    public static function make($data, $rules) {
        $errors = [];
        foreach ($rules as $field => $rule) {
            if ($rule === 'required' && empty($data[$field])) {
                $errors[$field][] = 'This field is required';
            }
        }
        return $errors;
    }
}