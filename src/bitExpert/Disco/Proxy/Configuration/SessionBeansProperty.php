<?php

/*
 * This file is part of the Disco package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace bitExpert\Disco\Proxy\Configuration;

use ProxyManager\Generator\Util\UniqueIdentifierGenerator;
use Zend\Code\Generator\PropertyGenerator;

/**
 * Private property to store the session beans (which which gets persisted after the request).
 */
class SessionBeansProperty extends PropertyGenerator
{
    /**
     * Creates a new {@link \bitExpert\Disco\Proxy\Configuration\SessionBeansProperty}.
     */
    public function __construct()
    {
        parent::__construct(UniqueIdentifierGenerator::getIdentifier('sessionBeans'));

        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setDocBlock('@var object[] contains all the references to session-aware beans.');
    }
}
