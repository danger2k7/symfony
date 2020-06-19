<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Notifier\Bridge\ISendPro;

use Symfony\Component\Notifier\Exception\LogicException;
use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Bogdan SOOS <bogdan.soos@dynweb.org>
 *
 * @experimental in 5.1
 */
final class ISendProTransport extends AbstractTransport
{
    protected const HOST = 'https://apirest.isendpro.com/cgi-bin/sms';

    private $apiKey;
    private $sender;

    public function __construct(string $login, string $sender, HttpClientInterface $client = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->apiKey = $login;
        $this->sender = $sender;

        parent::__construct($client, $dispatcher);
    }

    public function __toString(): string
    {
        return sprintf('isendpro://%s?from=%s', $this->getEndpoint(), $this->sender);
    }

    /**
     * @param MessageInterface $message
     * @return bool
     */
    public function supports(MessageInterface $message): bool
    {
        return $message instanceof SmsMessage;
    }

    /**
     * @param MessageInterface $message
     * @throws TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    protected function doSend(MessageInterface $message): void
    {
        if (!$this->supports($message)) {
            throw new LogicException(sprintf('The "%s" transport only supports instances of "%s" (instance of "%s" given) and configured with your phone number.', __CLASS__, SmsMessage::class, \get_class($message)));
        }

        $response = $this->client->request('POST', $this->getEndpoint(), [
            'json' => [
                'keyid' => $this->apiKey,
                'num' => $message->getPhone(),
                'emetteur' => $this->sender,
                'sms' => $message->getSubject(),
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            $error = $response->toArray(false);

            throw new TransportException(sprintf('Unable to send the SMS: "%s" (see "%s").', $error['message'], $error['more_info']), $response);
        }
    }
}
