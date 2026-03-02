<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Form\DoubleSubmitProtection\RequestTokenProvider;

interface StorageInterface
{
    public function getToken(string $formName): ?string;

    public function deleteToken(string $formName): void;

    public function setToken(string $formName, string $token): void;
}
