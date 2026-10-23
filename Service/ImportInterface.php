<?php

declare(strict_types=1);

/*
 * This file is part of FriendsOfSulu/SuluImportExportBundle.
 *
 * (c) FriendsOfSulu
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FriendsOfSulu\Bundle\SuluImportExportBundle\Service;

interface ImportInterface
{
    public function importPHPCR(): void;

    public function importDatabase(): void;

    public function importUploads(): void;
}
