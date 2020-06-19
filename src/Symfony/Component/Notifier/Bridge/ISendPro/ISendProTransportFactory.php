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

use Symfony\Component\Notifier\Exception\IncompleteDsnException;
use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Transport\Dsn;
use Symfony\Component\Notifier\Transport\TransportInterface;

class ISendProTransportFactory extends AbstractTransportFactory
{
    /**
     *
     */
    private const SUPPORTED_SCHEME = 'isendpro';

    /**
     * @inheritDoc
     */
    protected function getSupportedSchemes(): array
    {
        return [self::SUPPORTED_SCHEME];
    }

    /**
     * @param Dsn $dsn
     * @return ISendProTransport
     */
    public function create(Dsn $dsn): TransportInterface
    {
        $scheme = $dsn->getScheme();
        $login = $this->getUser($dsn);
        $from = (string)$dsn->getOption('from');

        if (empty($from)) {
            throw new IncompleteDsnException('Missing from information.');
        }

        if ('isendpro' === $scheme) {
            return new ISendProTransport($login, $from, $this->client, $this->dispatcher);
        }

        throw new UnsupportedSchemeException($dsn, self::SUPPORTED_SCHEME, $this->getSupportedSchemes());
    }
}