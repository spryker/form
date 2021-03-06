<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Form\DoubleSubmitProtection\RequestTokenProvider;

class TokenHashGenerator implements TokenGeneratorInterface
{
    /**
     * @var string
     */
    protected $algorithm;

    /**
     * @param string $algorithm
     */
    public function __construct(string $algorithm = self::DEFAULT_ALGORITHM)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @return string
     */
    public function generateToken(): string
    {
        return hash($this->algorithm, microtime() . mt_rand());
    }

    /**
     * @param string $expected
     * @param string $actual
     *
     * @return bool
     */
    public function checkTokenEquals(string $expected, string $actual): bool
    {
        return hash_equals($expected, $actual);
    }
}
