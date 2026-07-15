<?php
namespace Qadamchi\Validation;

use Qadamchi\Database\DB;
use Qadamchi\Database\Model;

/**
 * Validator (Laravel'ning validate g'oyasi).
 * Qoidalar: required, nullable, string, integer, numeric, email, min, max,
 * in, not_in, regex, url, date, confirmed, unique (fail-closed).
 */
class Validator
{
    protected array $data;
    protected array $rules;
    protected array $messages;
    protected array $errors = [];
    protected ?Model $context = null;

    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
    }

    public static function make(array $data, array $rules, array $messages = []): self
    {
        return (new self($data, $rules, $messages))->validate();
    }

    /** Validatsiyadan o'tgan (faqat qoidada bor) maydonlar. */
    public function validated(): array
    {
        return array_intersect_key($this->data, $this->rules);
    }

    public function validate(): self
    {
        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $rules = is_string($ruleString) ? explode('|', $ruleString) : $ruleString;

            $nullable = in_array('nullable', $rules, true);
            if ($nullable && ($value === null || $value === '')) {
                continue;
            }

            foreach ($rules as $rule) {
                if ($rule === 'nullable') continue;
                $this->applyRule($field, $rule, $value);
            }
        }
        return $this;
    }

    protected function applyRule(string $field, string $rule, $value): void
    {
        $params = [];
        if (strpos($rule, ':') !== false) {
            [$rule, $paramString] = explode(':', $rule, 2);
            $params = explode(',', $paramString);
        }

        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && count($value) === 0)) {
                    $this->addError($field, 'required');
                }
                break;
            case 'string':
                if ($value !== null && !is_string($value)) $this->addError($field, 'string');
                break;
            case 'integer':
                if ($value !== null && !filter_var($value, FILTER_VALIDATE_INT) && $value !== '0' && $value !== 0) $this->addError($field, 'integer');
                break;
            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) $this->addError($field, 'numeric');
                break;
            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) $this->addError($field, 'email');
                break;
            case 'url':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) $this->addError($field, 'url');
                break;
            case 'min':
                $min = (int) $params[0];
                $len = is_array($value) ? count($value) : strlen((string) $value);
                if ($value !== null && $value !== '' && $len < $min) $this->addError($field, 'min', ['min' => $min]);
                break;
            case 'max':
                $max = (int) $params[0];
                $len = is_array($value) ? count($value) : strlen((string) $value);
                if ($value !== null && $len > $max) $this->addError($field, 'max', ['max' => $max]);
                break;
            case 'in':
                if ($value !== null && !in_array($value, $params, true)) $this->addError($field, 'in');
                break;
            case 'not_in':
                if ($value !== null && in_array($value, $params, true)) $this->addError($field, 'not_in');
                break;
            case 'regex':
                if ($value !== null && $value !== '' && !preg_match('#' . $params[0] . '#', (string) $value)) $this->addError($field, 'regex');
                break;
            case 'date':
                if ($value !== null && $value !== '' && strtotime((string) $value) === false) $this->addError($field, 'date');
                break;
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== null && (!isset($this->data[$confirmField]) || $this->data[$confirmField] !== $value)) {
                    $this->addError($field, 'confirmed');
                }
                break;
            case 'unique':
                if (isset($params[0]) && $value !== null && $value !== '' && $this->isNotUnique($field, $params, $value)) {
                    $this->addError($field, 'unique');
                }
                break;
        }
    }

    /** Fail-closed: DB xatosi yoki takroriy qiymat — rad qilamiz. */
    protected function isNotUnique(string $field, array $params, $value): bool
    {
        $table = $params[0];
        $column = $params[1] ?? $field;
        $exceptColumn = $params[2] ?? null;
        $exceptValue = $params[3] ?? null;

        try {
            $sql = "SELECT COUNT(*) FROM `$table` WHERE `$column` = ?";
            $bindings = [$value];
            if ($exceptColumn && $exceptValue !== null) {
                $sql .= " AND `$exceptColumn` != ?";
                $bindings[] = $exceptValue;
            }
            $stmt = DB::connection()->prepare($sql);
            $stmt->execute($bindings);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Throwable $e) {
            // Fail-closed: tekshira olmasak — noqonuniy deb hisoblaymiz
            return true;
        }
    }

    protected function addError(string $field, string $rule, array $params = []): void
    {
        $this->errors[$field][] = $this->getMessage($field, $rule, $params);
    }

    protected function getMessage(string $field, string $rule, array $params = []): string
    {
        $key = "$field.$rule";
        if (isset($this->messages[$key])) return $this->messages[$key];

        $defaults = [
            'required'  => "$field maydoni majburiy.",
            'string'    => "$field matn bo'lishi kerak.",
            'integer'   => "$field butun son bo'lishi kerak.",
            'numeric'   => "$field son bo'lishi kerak.",
            'email'     => "$field to'g'ri email manzil bo'lishi kerak.",
            'url'       => "$field to'g'ri URL bo'lishi kerak.",
            'min'       => "$field kamida {$params['min']} ta belgi bo'lishi kerak.",
            'max'       => "$field ko'pi bilan {$params['max']} ta belgi bo'lishi kerak.",
            'in'        => "$field tanlangan qiymatlardan biri bo'lishi kerak.",
            'not_in'    => "$field bu qiymatga ega bo'lmasligi kerak.",
            'regex'     => "$field formati noto'g'ri.",
            'date'      => "$field to'g'ri sana bo'lishi kerak.",
            'unique'    => "$field allaqachon ishlatilgan.",
            'confirmed' => "$field tasdiqlash mos kelmaydi.",
        ];
        return $defaults[$rule] ?? "$field $rule qoidasiga mos kelmaydi.";
    }

    public function errors(): array { return $this->errors; }
    public function fails(): bool   { return !empty($this->errors); }
    public function passes(): bool  { return empty($this->errors); }
}