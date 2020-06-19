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

use Symfony\Component\Notifier\Bridge\ISendPro\ISendProTransportFactory;
use Symfony\Component\Notifier\Exception\IncompleteDsnException;
use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\Dsn;

final class ISendProTransportFactoryTest extends TestCase
{
    public function testCreateWithDsn(): void
    {
        $factory = $this->initFactory();

        $dsn = 'isendpro://apikey@default?from=symfony';
        $transport = $factory->create(Dsn::fromString($dsn));
        $transport->setHost('host.test');

        $this->assertSame('isendpro://apikey@default?from=symfony', (string) $transport);
    }

    public function testCreateWithNoSenderThrowsMalformed(): void
    {
        $factory = $this->initFactory();

        $this->expectException(IncompleteDsnException::class);

        $dsnIncomplete = 'isendpro://aipkey@default';
        $factory->create(Dsn::fromString($dsnIncomplete));
    }

    public function testSupportsISendProScheme(): void
    {
        $factory = $this->initFactory();

        $dsn = 'isendpro://apikey@default?from=symfony';
        $dsnUnsupported = 'foobarsender://apikey@default?from=symfony';

        $this->assertTrue($factory->supports(Dsn::fromString($dsn)));
        $this->assertFalse($factory->supports(Dsn::fromString($dsnUnsupported)));
    }

    public function testNonISendProSchemeThrows(): void
    {
        $factory = $this->initFactory();

        $this->expectException(UnsupportedSchemeException::class);

        $dsnUnsupported = 'foobarsender://apikey@default?from=symfony4';
        $factory->create(Dsn::fromString($dsnUnsupported));
    }

    private function initFactory(): ISendProTransportFactory
    {
        return new ISendProTransportFactory();
    }
}
