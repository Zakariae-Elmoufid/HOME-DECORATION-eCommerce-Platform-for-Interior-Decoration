<?php
namespace App\core;

class Validator {
    private $data = [];
    private $errors = [];
    private $rules = [];
    private $messages = [];

    /**
     * Validator constructor
     * 
     * @param array $data Data to be validated
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Set validation rules
     * 
     * @param array $rules Validation rules
     * @return self
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Set custom validation messages
     * 
     * @param array $messages Custom validation messages
     * @return self
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Validate the data
     * 
     * @return bool
     */
    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $rules = explode('|', $fieldRules);

            foreach ($rules as $rule) {
                $this->validateRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Validate a specific rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param string $rule Rule to apply
     */
    private function validateRule(string $field, $value, string $rule)
    {
        // Extract rule parameters
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleParam = $ruleParts[1] ?? null;

        // Validation methods
        $validationMethods = [
            'required' => function($val) {
                return $val !== null && trim($val) !== '';
            },
            'email' => function($val) {
                return $val === null || filter_var($val, FILTER_VALIDATE_EMAIL) !== false;
            },
            'min' => function($val, $param) {
                return $val === null || strlen($val) >= $param;
            },
            'max' => function($val, $param) {
                return $val === null || strlen($val) <= $param;
            },
            'numeric' => function($val) {
                return $val === null || is_numeric($val);
            },
            'integer' => function($val) {
                return $val === null || filter_var($val, FILTER_VALIDATE_INT) !== false;
            },
            'confirmed' => function($val, $param, $data) {
                $confirmField = $param ?? $field . '_confirmation';
                return $val === ($data[$confirmField] ?? null);
            },
            'unique' => function($val, $param, $data, $repository) {
                if ($val === null) return true;
                
                // Split parameter into table and column
                list($table, $column) = explode(',', $param);
                
                // Check uniqueness via repository
                return !$repository->existsByColumn($table, $column, $val);
            },
            'contains' => function($val, $param) {
                if ($val === null) return false;
                
                switch ($param) {
                    case 'uppercase':
                        return preg_match('/[A-Z]/', $val) === 1;
                    case 'lowercase':
                        return preg_match('/[a-z]/', $val) === 1;
                    case 'number':
                        return preg_match('/[0-9]/', $val) === 1;
                    case 'special':
                        return preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $val) === 1;
                    default:
                        return strpos($val, $param) !== false;
                }
            }
        ];

        // Validate the rule
        $isValid = true;
        if (isset($validationMethods[$ruleName])) {
            // Handle rules with different parameters
            switch ($ruleName) {
                case 'confirmed':
                    $isValid = $validationMethods[$ruleName]($value, $ruleParam, $this->data);
                    break;
                case 'unique':
                    $repository = new BaseRepository();
                    $isValid = $validationMethods[$ruleName]($value, $ruleParam, $this->data, $repository);
                    break;
                case 'min':
                case 'max':
                case 'contains':
                    $isValid = $validationMethods[$ruleName]($value, $ruleParam);
                    break;
                default:
                    $isValid = $validationMethods[$ruleName]($value);
            }
        }

        // Handle errors
        if (!$isValid) {
            $this->addError($field, $this->getErrorMessage($field, $ruleName, $ruleParam));
        }
    }

    /**
     * Generate an error message
     * 
     * @param string $field Field with error
     * @param string $rule Rule that was not met
     * @param string|null $param Rule parameter
     * @return string
     */
    private function getErrorMessage(string $field, string $rule, ?string $param = null): string
    {
        // Default error messages
        $defaultMessages = [
            'required' => "The {$field} field is required.",
            'email' => "The {$field} field must be a valid email address.",
            'min' => "The {$field} field must have at least {$param} characters.",
            'max' => "The {$field} field must have at most {$param} characters.",
            'numeric' => "The {$field} field must be a number.",
            'integer' => "The {$field} field must be an integer.",
            'confirmed' => "The confirmation for {$field} does not match.",
            'unique' => "The value for {$field} is already in use.",
            'contains' => "The {$field} field must contain {$param}."
        ];

        // Check for custom messages
        $customKey = "{$field}.{$rule}";
        if (isset($this->messages[$customKey])) {
            return $this->messages[$customKey];
        }

        return $defaultMessages[$rule] ?? "Validation error for the {$field} field.";
    }

    /**
     * Add a validation error
     * 
     * @param string $field Field with error
     * @param string $message Error message
     */
    private function addError(string $field, string $message)
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Get validation errors
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get validated data
     * 
     * @return array
     */
    public function getValidatedData(): array
    {
        $validatedData = [];
        foreach (array_keys($this->rules) as $field) {
            $validatedData[$field] = $this->data[$field] ?? null;
        }
        return $validatedData;
    }
}
