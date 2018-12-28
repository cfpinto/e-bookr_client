<?php

namespace Ebookr\Client\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    private $body;

    private $fromName;

    private $fromEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($from, $subject, $message)
    {
        if (!is_array($from)) {
            $from = [$from, null];
        }
        $this->subject($subject);
        $this->from($from[0], $from[1]);

        $this->body = $message;
        $this->subject = $subject;
        $this->fromName = $from[1] ?? $from[0];
        $this->fromEmail = $from[0];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contact')
            ->text('emails.contact_plain')
            ->with(
                [
                    'body'      => $this->body,
                    'fromName'  => $this->fromName,
                    'fromEmail' => $this->fromEmail,
                    'subject'   => $this->subject,
                ]
            );
    }
}
