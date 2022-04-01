<?php

namespace App\Foundation\Validate;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Factory;

trait InteractsValidate
{
    /**
     * Validate the given request with the given rules.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @param bool $stopOnFirstFailure
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $data, array $rules, array $messages = [], array $customAttributes = [], $stopOnFirstFailure = false)
    {
        return $this->getValidationFactory()->make(
            $data,
            $rules,
            $messages,
            $customAttributes
        )->stopOnFirstFailure($stopOnFirstFailure)
            ->setException(ValidationException::class)
            ->validate();
    }

    /**
     * Get a validation factory instance.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     */
    protected function getValidationFactory()
    {
        return app(Factory::class);
    }
}
