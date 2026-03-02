<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Form;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\Form\FormConfig getConfig()
 */
class FormDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const PLUGINS_FORM = 'PLUGINS_FORM';

    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = $this->addFormPlugins($container);

        return $container;
    }

    protected function addFormPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_FORM, function () {
            return $this->getFormPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Shared\FormExtension\Dependency\Plugin\FormPluginInterface>
     */
    protected function getFormPlugins(): array
    {
        return [];
    }
}
