<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PaymentMethodChangerPolyfillPass implements CompilerPassInterface
{
    private const OLD_NAME = 'Sylius\Bundle\ApiBundle\Changer\PaymentMethodChanger';

    private const NEW_NAME = 'Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::OLD_NAME)) {
            $container->setAlias(self::OLD_NAME, self::NEW_NAME);
        }

        if (!$container->has('sylius.api.context.user')) {
            $container->setAlias('sylius.api.context.user', 'Sylius\Bundle\ApiBundle\Context\UserContextInterface');
        }
    }
}
