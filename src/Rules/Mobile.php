<?php

namespace Ebookr\Client\Rules;

use Illuminate\Contracts\Validation\Rule;

class Mobile implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/^\+[0-9]+/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Numero de telefone inválido. Os numeros de telemovel devem começar com + (mais) seguido de numeros. Ex: +351000000000');
    }
}