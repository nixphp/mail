<?php

namespace NixPHP\Mail\Core;

use NixPHP\Mail\Exceptions\MailException;

class Mailer
{
    protected array $recipients = [];
    protected array $carbonCopies = [];
    protected array $blindCarbonCopies = [];

    protected string $from = '';
    protected string $replyTo = '';
    protected string $subject = '';
    protected string $content = '';
    protected bool $isHtml = true;
    protected array $attachments = [];

    protected ?TransportInterface $transport = null;

    public function __construct(?TransportInterface $transport = null)
    {
        $this->transport = $transport;
    }

    public function setTransport(TransportInterface $transport): void
    {
        $this->transport = $transport;
    }

    public function addTo(string $address): void
    {
        $this->recipients[] = $address;
    }

    public function addCc(string $address): void
    {
        $this->carbonCopies[] = $address;
    }

    public function addBcc(string $address): void
    {
        $this->blindCarbonCopies[] = $address;
    }

    public function setFrom(string $address): void
    {
        $this->from = $address;
    }

    public function setReplyTo(string $address): void
    {
        $this->replyTo = $address;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function setContent(string $content, bool $isHtml = true): void
    {
        $this->content = $content;
        $this->isHtml = $isHtml;
    }

    public function addAttachment(string $name, string $path, bool $inline = false): void
    {
        if (!is_file($path)) {
            throw new MailException("Attachment file not found: $path");
        }

        $this->attachments[] = [
            'name'     => $name,
            'path'     => $path,
            'encoded'  => chunk_split(base64_encode(file_get_contents($path))),
            'mimetype' => mime_content_type($path),
            'inline'   => $inline,
        ];
    }

    public function send(): bool
    {
        if (!$this->transport) {
            throw new MailException('No mail transport defined.');
        }

        return $this->transport->sendMail($this);
    }

    // Getters for use in Transport
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function getCc(): array
    {
        return $this->carbonCopies;
    }

    public function getBcc(): array
    {
        return $this->blindCarbonCopies;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getReplyTo(): string
    {
        return $this->replyTo ?: $this->from;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isHtml(): bool
    {
        return $this->isHtml;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }
}
