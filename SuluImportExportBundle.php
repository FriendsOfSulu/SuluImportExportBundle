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

namespace TheCadien\Bundle\SuluImportExportBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TheCadien\Bundle\SuluImportExportBundle\DependencyInjection\Compiler\DbConnectionPass;

class SuluImportExportBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DbConnectionPass());
    }
}
