<?php
class Validator {
    private $data;
    private $rules;
    private $messages;
    private $errors = [];
    
    public function __construct($data, $rules, $messages = []) {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
    }
    
    public static function make($data, $rules, $messages = []) {
        $validator = new static($data, $rules, $messages);
        $validator->validate();
        return $validator;
    }
    
    public function validate() {
        foreach ($this->rules as $field => $ruleString) {
            if (empty($ruleString)) continue;
            
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;
            
            foreach ($rules as $rule) {
                $this->applyRule($field, $rule, $value);
            }
        }
        
        return $this;
    }
    
    private function applyRule($field, $rule, $value) {
        $parameters = [];
        if (strpos($rule, ':') !== false) {
            [$rule, $paramString] = explode(':', $rule, 2);
            $parameters = explode(',', $paramString);
        }
        
        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, 'required');
                }
                break;
                
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'email');
                }
                break;
                
            case 'min':
                $min = (int) $parameters[0];
                if ($value && strlen($value) < $min) {
                    $this->addError($field, 'min', ['min' => $min]);
                }
                break;
                
            case 'max':
                $max = (int) $parameters[0];
                if ($value && strlen($value) > $max) {
                    $this->addError($field, 'max', ['max' => $max]);
                }
                break;
                
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value && (!isset($this->data[$confirmField]) || $this->data[$confirmField] !== $value)) {
                    $this->addError($field, 'confirmed');
                }
                break;
                
            case 'unique':
                if (isset($parameters[0]) && $value) {
                    $table = $parameters[0];
                    $column = $parameters[1] ?? $field;
                    if ($this->isNotUnique($table, $column, $value)) {
                        $this->addError($field, 'unique');
                    }
                }
                break;
        }
    }
    
    private function isNotUnique($table, $column, $value) {
        try {
            $db = require __DIR__.'/../config/db.php';
            $pdo = new PDO("mysql:host={$db['host']};dbname={$db['name']}", $db['user'], $db['pass']);
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
            $stmt->execute([$value]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false; // Database xatoligida unique deb hisoblaymiz
        }
    }
    
    private function addError($field, $rule, $parameters = []) {
        $message = $this->getMessage($field, $rule, $parameters);
        $this->errors[$field][] = $message;
    }
    
    private function getMessage($field, $rule, $parameters = []) {
        $key = "$field.$rule";
        if (isset($this->messages[$key])) {
            return $this->messages[$key];
        }
        
        $defaultMessages = [
            'required' => "$field maydoni majburiy.",
            'email' => "$field to'g'ri email manzil bo'lishi kerak.",
            'min' => "$field kamida {$parameters['min']} ta belgi bo'lishi kerak.",
            'max' => "$field ko'pi bilan {$parameters['max']} ta belgi bo'lishi kerak.",
            'unique' => "$field allaqachon ishlatilgan.",
            'confirmed' => "$field tasdiqlash mos kelmaydi."
        ];
        
        return $defaultMessages[$rule] ?? "$field $rule qoidasiga mos kelmaydi.";
    }
    
    public function errors() {
        return $this->errors;
    }
    
    public function fails() {
        return !empty($this->errors);
    }
    
    public function passes() {
        return empty($this->errors);
    }
}