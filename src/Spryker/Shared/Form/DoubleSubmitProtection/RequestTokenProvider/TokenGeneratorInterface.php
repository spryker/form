<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Form\DoubleSubmitProtection\RequestTokenProvider;

interface TokenGeneratorInterface
{
    /**
     * @var string
     */
    public const DEFAULT_ALGORITHM = 'sha256';

    /**
     * @return string
     */
    public function generateToken(): string;

    /**
     * @param string $expected
     * @param string $actual
     *
     * @return bool
     */
    public function checkTokenEquals(string $expected, string $actual): bool;
}
