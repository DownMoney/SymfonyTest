<?php

namespace AppBundle;

class Mailer
{
    private $method;

    public function __construct($method)
    {
        $this->method = $method;
    }

    public function send($email, $content){
        return "Sending an email to ".$email." using ".$this->method;
    }
}