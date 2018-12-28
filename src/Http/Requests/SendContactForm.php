<?php

namespace Ebookr\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendContactForm extends FormRequest
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
            'name'    => 'required',
            'email'   => 'required|email',
            'subject' => 'required',
            'message' => 'required|min:20',
        ];
    }

    public function messages()
    {
        return [
            'name.required'    => __('Este campo é obrigatório'),
            'email.required'   => __('Este campo é obrigatório'),
            'email.email'      => __('Este campo deve ter o seguinte formato: mail@domain.tls'),
            'subject.required' => __('Este campo é obrigatório'),
            'message.required' => __('Este campo é obrigatório'),
            'message.min'      => __('Este campo deve ter um minimo de 20 caracteres'),
        ];
    }
}
