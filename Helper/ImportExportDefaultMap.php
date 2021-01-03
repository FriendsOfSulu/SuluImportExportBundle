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

namespace TheCadien\Bundle\SuluImportExportBundle\Helper;

class ImportExportDefaultMap
{
    public const FILENAME_PHPCR = 'export.phpcr';

    public const FILENAME_SQL = 'export.sql';

    public const FILENAME_UPLOADS = 'uploads.tar.gz';

    public const SULU_DEFAULT_MEDIA_PATH = 'var/uploads/media';
}
