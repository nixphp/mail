<?php

namespace NixPHP\Mail\Core\Transport;

use NixPHP\Mail\Core\Mailer;
use NixPHP\Mail\Core\TransportInterface;
use NixPHP\Mail\Exceptions\MailException;

class MailTransport implements TransportInterface
{

    public function sendMail(Mailer $mailer): bool
    {
        $to = implode(',', $mailer->getRecipients());
        $subject = mb_encode_mimeheader($mailer->getSubject(), 'UTF-8');

        $boundary = md5(uniqid((string)mt_rand(), true));
        $eol = "\r\n";

        // Headers
        $headers = 'MIME-Version: 1.0' . $eol;
        $headers .= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"' . $eol;
        $headers .= 'From: ' . $mailer->getFrom() . $eol;
        $headers .= 'Reply-To: ' . $mailer->getReplyTo() . $eol;

        if ($cc = $mailer->getCc()) {
            $headers .= 'Cc: ' . implode(',', $cc) . $eol;
        }

        if ($bcc = $mailer->getBcc()) {
            $headers .= 'Bcc: ' . implode(',', $bcc) . $eol;
        }

        // Body
        $body = '--' . $boundary . $eol;
        $body .= 'Content-Type: ' . ($mailer->isHtml() ? 'text/html' : 'text/plain') . '; charset="utf-8"' . $eol;
        $body .= 'Content-Transfer-Encoding: 8bit' . $eol . $eol;
        $body .= $mailer->getContent() . $eol;

        foreach ($mailer->getAttachments() as $attachment) {
            $body .= '--' . $boundary . $eol;
            $body .= 'Content-Type: ' . $attachment['mimetype'] . '; name="' . $attachment['name'] . '"' . $eol;
            if ($attachment['inline']) {
                $body .= 'Content-ID: <' . $attachment['name'] . '>' . $eol;
                $body .= 'Content-Disposition: inline; filename="' . $attachment['name'] . '"' . $eol;
            } else {
                $body .= 'Content-Disposition: attachment; filename="' . $attachment['name'] . '"' . $eol;
            }
            $body .= 'Content-Transfer-Encoding: base64' . $eol;
            $body .= 'X-Attachment-Id: ' . uniqid() . $eol . $eol;
            $body .= $attachment['encoded'] . $eol;
        }

        $body .= '--' . $boundary . '--' . $eol;

        if (!mail($to, $subject, $body, $headers)) {
            throw new MailException('Unable to send mail using PHP mail()');
        }

        return true;
    }

}