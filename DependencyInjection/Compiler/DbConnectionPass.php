<?php

declare(strict_types=1);

/*
 * This file is part of TheCadien/SuluImportExportBundle.
 *
 * (c) Oliver Kossin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace TheCadien\Bundle\SuluImportExportBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This compiler pass is responsible for fetching the connection params
 * of the DBAL and passes them to our export and import service.
 */
class DbConnectionPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $connectionName = $container->getParameter('sulu_import_export_bundle.dbal_connection');

        if (!$connectionName) {
            $connectionName = $container->getParameter('doctrine.default_connection');
        }

        $connections = $container->getParameter('doctrine.connections');

        if (!isset($connections[$connectionName])) {
            throw new \InvalidArgumentException(sprintf('There is no doctrine connection configured which is named "%s". Available names are "%s".', $connectionName, implode('", "', array_keys($connections))));
        }

        $connectionId = $connections[$connectionName];
        $connectionParams = $container->getDefinition($connectionId)->getArgument(0);

        $exportService = $container->getDefinition('sulu.service.export');
        $exportService->setArgument('$databaseParams', $connectionParams);

        $importService = $container->getDefinition('sulu.service.import');
        $importService->setArgument('$databaseParams', $connectionParams);
    }
}
