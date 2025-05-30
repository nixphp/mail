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

    public function setTransport(TransportInterface $transport): static
    {
        $this->transport = $transport;
        return $this;
    }

    public function addTo(string $address): static
    {
        $this->recipients[] = $address;
        return $this;
    }

    public function addCc(string $address): static
    {
        $this->carbonCopies[] = $address;
        return $this;
    }

    public function addBcc(string $address): static
    {
        $this->blindCarbonCopies[] = $address;
        return $this;
    }

    public function setFrom(string $address): static
    {
        $this->from = $address;
        return $this;
    }

    public function setReplyTo(string $address): static
    {
        $this->replyTo = $address;
        return $this;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function setContent(string $content, bool $isHtml = true): static
    {
        $this->content = $content;
        $this->isHtml = $isHtml;
        return $this;
    }

    public function addAttachment(string $name, string $path, bool $inline = false): static
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

        return $this;
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
