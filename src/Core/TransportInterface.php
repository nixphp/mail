<?php

namespace NixPHP\Mail\Core;

interface TransportInterface
{

    public function sendMail(Mailer $mailer);

}