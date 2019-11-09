<?php

namespace Ebookr\Client\Http\Requests;

use Ebookr\Client\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;

class StoreBooking extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => 'required|min:5',
            'start'    => 'required|date',
            'duration' => 'required|int',
            'adults'   => 'required|int',
            'children' => 'required|int',
            'email'    => 'required|email',
            'mobile'   => ['required', new Mobile],
        ];
    }
    
    public function messages()
    {
        return [
            'name.required' => __('Este campo é obrigatório'),
            'email.required' => __('Este campo é obrigatório'),
            'mobile.required' => __('Este campo é obrigatório'),
            'email.email'      => __('Este campo deve ter o seguinte formato: mail@domain.tls'),
        ];
    }
}
