<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\DependencyInjection\Compiler;

use DMKClub\Bundle\BasicsBundle\PDF\Manager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds tagged dmkclub_basics.pdfgenerator services to pdf service.
 */
class PdfGeneratorPass implements CompilerPassInterface
{
    public const string TAG = 'dmkclub_basics.pdfgenerator';
    public const string MANAGER = Manager::class;

    public function process(ContainerBuilder $container): void
    {
        if (false === $container->hasDefinition(self::MANAGER)) {
            return;
        }

        $definition = $container->getDefinition(self::MANAGER);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach (array_keys($taggedServices) as $id) {
            $definition->addMethodCall(
                'addGenerator',
                [new Reference($id)]
            );
        }
    }
}
