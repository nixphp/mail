<?php

use NixPHP\Mail\Core\Mailer;
use NixPHP\Mail\Core\Transport\MailTransport;
use function NixPHP\app;

app()->container()->set('mail', function() {
    return new Mailer(new MailTransport());
});