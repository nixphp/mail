<?php

namespace NixPHP\Mail;

use NixPHP\Mail\Core\Mailer;
use function NixPHP\app;

function mailer(): Mailer
{
    return app()->container()->get('mailer');
}