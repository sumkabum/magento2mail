<?php

namespace Sumkabum\Magento2mail\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterface;
use Magento\Framework\Mail\MimePartInterfaceFactory;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $to;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var array
     */
    protected $templateOptions = [];

    /**
     * @var array
     */
    protected $templateVars = [];

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @param string $to
     * @return TransportBuilder
     */
    public function setTo(string $to): self
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return TransportBuilder
     */
    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return TransportBuilder
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    protected function prepareMessage()
    {
        /** @var AddressConverter $addressConverter */
        $addressConverter = ObjectManager::getInstance()->create(AddressConverter::class);
        $sender = $this->getSenderEmailFromConf();

        /** @var MimePartInterface $mimePart */
        $mimePart = ObjectManager::getInstance()->create(MimePartInterfaceFactory::class)->create(['content' => $this->getBody()]);
        $messageData['to'][] = $addressConverter->convert($this->getTo());
        $messageData['from'][] = $addressConverter->convert($sender);
        $messageData['encoding'] = $mimePart->getCharset();
        $messageData['body'] = ObjectManager::getInstance()->create(MimeMessageInterfaceFactory::class)->create(
            ['parts' => [$mimePart]]
        );

        $messageData['subject'] = $this->getSubject();

        $this->message = ObjectManager::getInstance()->create(EmailMessageInterfaceFactory::class)->create($messageData);
    }

    public function getSenderEmailFromConf(): string
    {
        /** @var ScopeConfigInterface $contactsConfig */
        $contactsConfig = ObjectManager::getInstance()->create(ScopeConfigInterface::class);
        return $contactsConfig->getValue('trans_email/ident_general/email');
    }
}
