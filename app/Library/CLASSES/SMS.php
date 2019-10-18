<?php

namespace App\Library\CLASSES;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log; //Login



class SMS
{

    public $twilio_number;
    public $account_sid;
    public $auth_token;

    public function __construct()
    {
        // Your Account SID and Auth Token from twilio.com/console
        $this->account_sid = env('TWILIO_CLIENT_ID');
        $this->auth_token = env('TWILIO_AUTH_TOKEN');
        // In production, these should be environment variables. E.g.:
        // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]

        // A Twilio number you own with SMS capabilities
        $this->twilio_number = env('TWILIO_NUMBER');

        
    }

    public function enviarMensaje($message, $cellNumber){

        Log::info('[SMS][enviarMensaje]: '. $this->account_sid);
        Log::info('[SMS][enviarMensaje]: '. $this->auth_token);
        $client = new Client($this->account_sid, $this->auth_token);

        return $client->messages->create(
            // Where to send a text message (your cell phone?)
            $cellNumber,
            array(
                'from' => $this->twilio_number,
                'body' => $message
            )
        );
    }
    
    

}

?>