<?php

namespace Ebookr\Client\Http\Controllers;

use Ebookr\Client\Http\Requests\SendContactForm;
use Ebookr\Client\Mail\Contact;

class FormController extends Controller
{
    public function contact(SendContactForm $request)
    {
        $mail = new Contact([$request->input('email'), $request->input('name')], $request->input('subject'), $request->input('message'));
        \Mail::to(env('MAIL_OVERRIDE', env('MAIL_MARKETING')))
            ->send($mail);
        
        flash()->success(__('Obrigado pelo seu contacto. Um membro da nossa equipa de apoio entrara em contacto consigo dentro de 1 dia comercial.'));
        
        return redirect()->back();
    }
}
