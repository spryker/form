<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Form\DoubleSubmitProtection\RequestTokenProvider;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStorage implements StorageInterface
{
    /**
     * @var string
     */
    protected const SESSION_KEY_PREFIX = 'req_';

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getToken(string $formName): ?string
    {
        return $this->session->get($this->buildSessionKey($formName));
    }

    public function deleteToken(string $formName): void
    {
        $this->session->remove($this->buildSessionKey($formName));
    }

    public function setToken(string $formName, string $token): void
    {
        $this->session->set($this->buildSessionKey($formName), $token);
    }

    protected function buildSessionKey(string $formName): string
    {
        return sprintf('%s%s', static::SESSION_KEY_PREFIX, $formName);
    }
}
