<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Notifier\Bridge\ISendPro\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Bridge\ISendPro\ISendProTransport;
use Symfony\Component\Notifier\Exception\LogicException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ISendProTransportTest extends TestCase
{
    public function testToStringContainsProperties(): void
    {
        $transport = $this->initTransport();

        $this->assertSame('isendpro://host.test?from=symfony', (string) $transport);
    }

    public function testSupportsMessageInterface(): void
    {
        $transport = $this->initTransport();

        $this->assertTrue($transport->supports(new SmsMessage('0611223344', 'Hello!')));
        $this->assertFalse($transport->supports(new SmsMessage('0699887766', 'Hello!')));
        $this->assertFalse($transport->supports($this->createMock(MessageInterface::class), 'Hello!'));
    }

    public function testSendNonSmsMessageThrowsException(): void
    {
        $transport = $this->initTransport();

        $this->expectException(LogicException::class);

        $transport->send(new SmsMessage('0699887766', 'Hello!'));
    }

    private function initTransport(): ISendProTransport
    {
        return (new ISendProTransport(
            'login', 'symfony', $this->createMock(HttpClientInterface::class)
        ))->setHost('host.test');
    }
}
