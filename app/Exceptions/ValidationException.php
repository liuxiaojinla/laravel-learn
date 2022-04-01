<?php

namespace App\Exceptions;

class ValidationException extends \Illuminate\Validation\ValidationException
{
    /**
     * @param string $message
     * @param string $errorBag
     * @return ValidationException
     */
    public static function withMessage(string $message, string $errorBag = 'default'): ValidationException
    {
        return self::withMessages([
            $errorBag => [$message],
        ]);
    }

    /**
     * Create a error message summary from the validation errors.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return string
     */
    protected static function summarize($validator)
    {
        $messages = $validator->errors()->all();

        if (!count($messages)) {
            return '给定的数据无效。';
        }

        return array_shift($messages);
    }
}