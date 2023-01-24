<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Form\Plugin\Application;

use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface;
use Spryker\Yves\Kernel\AbstractPlugin;
use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\ClearableTokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

/**
 * @method \Spryker\Yves\Form\FormFactory getFactory()
 * @method \Spryker\Yves\Form\FormConfig getConfig()
 */
class FormApplicationPlugin extends AbstractPlugin implements ApplicationPluginInterface
{
    /**
     * @var string
     */
    protected const SERVICE_FORM_FACTORY = 'form.factory';

    /**
     * @var string
     */
    protected const SERVICE_FORM_CSRF_PROVIDER = 'form.csrf_provider';

    /**
     * @var string
     */
    protected const SERVICE_SESSION = 'session';

    /**
     * @var string
     */
    protected const SERVICE_FORM_FACTORY_ALIAS = 'FORM_FACTORY';

    /**
     * {@inheritDoc}
     * - Adds `form.factory` service.
     * - Adds global `FORM_FACTORY` service as an alias for `form.factory`.
     * - Adds `form.csrf_provider` service.
     *
     * @api
     *
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Service\Container\ContainerInterface
     */
    public function provide(ContainerInterface $container): ContainerInterface
    {
        $container = $this->addFormFactory($container);
        $container = $this->addFormCsrfProvider($container);

        return $container;
    }

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Service\Container\ContainerInterface
     */
    protected function addFormFactory(ContainerInterface $container): ContainerInterface
    {
        $container->set(static::SERVICE_FORM_FACTORY, function () use ($container) {
            $formFactoryBuilder = $this->getFactory()
                ->createFormFactoryBuilder();

            $formFactoryBuilder = $this->extendForm($formFactoryBuilder, $container);

            return $formFactoryBuilder->getFormFactory();
        });

        $container->configure(static::SERVICE_FORM_FACTORY, ['alias' => static::SERVICE_FORM_FACTORY_ALIAS, 'isGlobal' => true]);

        return $container;
    }

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Service\Container\ContainerInterface
     */
    protected function addFormCsrfProvider(ContainerInterface $container): ContainerInterface
    {
        $container->set(static::SERVICE_FORM_CSRF_PROVIDER, function (ContainerInterface $container) {
            return $this->createCsrfTokenManager($container);
        });

        return $container;
    }

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    protected function createCsrfTokenManager(ContainerInterface $container): CsrfTokenManagerInterface
    {
        return new CsrfTokenManager(
            null,
            $this->createTokenStorage($container),
        );
    }

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Symfony\Component\Security\Csrf\TokenStorage\ClearableTokenStorageInterface
     */
    protected function createTokenStorage(ContainerInterface $container): ClearableTokenStorageInterface
    {
        if ($container->has(static::SERVICE_SESSION)) {
            return $this->createSessionTokenStorage($container->get(static::SERVICE_SESSION));
        }

        return $this->getFactory()->createDefaultTokenStorage();
    }

    /**
     * @param \Symfony\Component\Form\FormFactoryBuilderInterface $formFactoryBuilder
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Symfony\Component\Form\FormFactoryBuilderInterface
     */
    protected function extendForm(FormFactoryBuilderInterface $formFactoryBuilder, ContainerInterface $container): FormFactoryBuilderInterface
    {
        foreach ($this->getFactory()->getFormPlugins() as $formPlugin) {
            $formFactoryBuilder = $formPlugin->extend($formFactoryBuilder, $container);
        }

        return $formFactoryBuilder;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     *
     * @return \Symfony\Component\Security\Csrf\TokenStorage\ClearableTokenStorageInterface
     */
    protected function createSessionTokenStorage(SessionInterface $session): ClearableTokenStorageInterface
    {
        $request = new Request();
        $request->setSession($session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        return new SessionTokenStorage($requestStack);
    }
}
