<?php


namespace App\Helpers;
use \Mail;
use \Kickbox\Client;

/**
 * Class Email
 * To send an email:
 * \App\Helpers\Email::send('david.slack@airangel.com', 'Test email', 'This is a test');
 *
 * @package App\Helpers
 */
class Email
{
    private $error = true;
    private $tags;
    private $to;
    private $from;
    private $subject;
    private $textBody;
    private $htmlBody;

    public function __construct($to = null, $subject = null, $textBody = null, $from = null, $htmlBody = null, $tags = null)
    {
        $this->to = $to;
        //if from address is empty, use the config from address
        $this->from = $from == null ? config('mail.from.address') : $from;
        $this->subject = $subject;
        $this->textBody = $textBody;
        $this->htmlBody = $htmlBody;
        $this->tags = $tags;

        // validate the email
        $this->validateEmail();

        // if htmlBody is not empty
        if($htmlBody != null){
            // sendHTMLEmail
            $this->sendHtmlEmail();
        }else{
            //if it is empty, send text email
            $this->sendTextEmail();
        }
    }

    /**
     * Validate Email Address
     * Checks email is valid using PHP, if it is it then uses the validator from the service set in $emailValidator
     * at the top of the page
     */
    private function validateEmail(){

        // check if email is valid in PHP
        if (!filter_var($this->to, FILTER_VALIDATE_EMAIL)){
            $this->error = 'invalid email';
        }
        // if emailValidator = mailgun send to mailgun validator
        elseif(config('mail.validator') == 'mailgun') {
            $this->validateEmailWithMailgun($this->to);
        }
        // if emailValidator = kickbox send to kickbox validator
        elseif(config('mail.validator') == 'kickbox'){
            $this->validateEmailWithKickbox($this->to);
        }

    }

    /**
     * Send email function
     *
     *
     * @param null $to
     * @param null $subject
     * @param null $textBody
     * @param null $from
     * @param null $htmlBody
     * @param null $tags
     * @return bool
     */
    public static function send($to = null, $subject = null,  $textBody = null, $from = null, $htmlBody = null, $tags = null)
    {
        $email = new self($to, $subject, $textBody, $from, $htmlBody, $tags);
        return $email->error;

    }

    /**
     * Send HTML email
     * Sets the data and sends the data to the view, the view is what will be emailed so this could potentially
     * be an email template view. This sends text and HTML to the view
     *
     */
    private function sendHtmlEmail()
    {
        $data = [
            'to'        => $this->to,
            'from'      => $this->from,
            'subject'   => $this->subject,
            'text'      => $this->textBody,
            'html'      => $this->htmlBody,
            'tags'  => $this->tags
        ];

        Mail::send(['html' => 'admin.modules.emails.html'], $data, function($message) {
            $message->subject($this->subject);
            $message->to($this->to);
            $message->from($this->from);
            $message->sender($this->from);
            $headers = $message->getHeaders();
            foreach($this->tags as $tag){
                $headers->addTextHeader('X-Mailgun-Tag', $tag);
            }

        });
        
    }

    /**
     * Send text email
     * Sets the data and sends the data to the view, the view is what will be emailed so this could potentially
     * be an email template view. This sends text to the view and not HTML.
     *
     */
    private function sendTextEmail()
    {
        $data = [
            'to'        => $this->to,
            'from'      => $this->from,
            'subject'   => $this->subject,
            'text'      => $this->textBody
        ];

        Mail::send(['text' => 'admin.modules.emails.text'], $data, function($message){
            $message->subject($this->subject);
            $message->to($this->to);
            $message->from($this->from);
            $message->sender($this->from);
            $headers = $message->getHeaders();
            foreach($this->tags as $tag){
                $headers->addTextHeader('X-Mailgun-Tag', $tag);
            }
        });
    }

    /**
     * Validate the Email using the Mailgun API
     * Gets the email from $this->to and checks if it is a valid email address
     */
    private function validateEmailWithMailgun()
    {
        $client = new \Http\Adapter\Guzzle6\Client();
        $mgClient = new \Mailgun\Mailgun(config('services.mailgun.public'), $client);
        $result = $mgClient->get("address/validate", array('address' => $this->to));
        $isValid = $result->http_response_body->is_valid;

        if($isValid == false)
            $this->error = 'Mailgun could not validate the email ' . $this->to;
    }

    /**
     * Check an email address using Kickbox API
     * Gets the email from $this->to and checks if it is a valid email address
     */
    private function validateEmailWithKickbox()
    {
        // Create a new kickbox
        $client = new Client(config('services.kickbox.key'));

        // Create the verification
        $kickbox  = $client->kickbox();
        $response = $kickbox->verify( $this->to );

        //dd($response->body['result']);
         //Return true or false
        if( $response->body['result'] == 'undeliverable' )
            $this->error = 'Kickbox could not validate the email ' . $this->to;
    }




}