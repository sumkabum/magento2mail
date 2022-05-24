<?php

namespace Sumkabum\Magento2mail\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Message;
use Magento\Framework\Mail\TransportInterfaceFactory;

class Mail
{
    /**
     * @throws MailException
     */
    public function send(string $to, string $subject, string $body, ?string $from = null)
    {
        if (!$from) {
            $from = $this->getSenderEmailFromConf();
        }

        /** @var Message $message */
        $message = ObjectManager::getInstance()->create(Message::class);

        $message
            ->setFrom($from)
            ->addTo($to)
            ->setSubject($subject)
            ->setBody($body);

        /** @var TransportInterfaceFactory $transportFactory */
        $transportFactory = ObjectManager::getInstance()->get(TransportInterfaceFactory::class);
        $transport = $transportFactory->create(['message' => $message]);
        $transport->sendMessage();
    }

    public function getSenderEmailFromConf(): string
    {
        /** @var ScopeConfigInterface $contactsConfig */
        $contactsConfig = ObjectManager::getInstance()->create(ScopeConfigInterface::class);
        return $contactsConfig->getValue('trans_email/ident_general/email');
    }
}
