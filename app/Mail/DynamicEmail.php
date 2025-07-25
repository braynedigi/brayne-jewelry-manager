<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynamicEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $htmlContent;

    /**
     * Create a new message instance.
     */
    public function __construct($subjectText, $htmlContent)
    {
        $this->subjectText = $subjectText;
        $this->htmlContent = $htmlContent;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subjectText)
                    ->html($this->htmlContent);
    }
} 