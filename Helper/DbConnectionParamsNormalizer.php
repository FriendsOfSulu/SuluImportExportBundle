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

class DbConnectionParamsNormalizer
{
    /**
     * Normalizes the array with the connection params to a defined structure.
     *
     * @param array $connectionParams
     *        The database connection params for the connection. Should be passed
     *        as an associative array with the key 'url' or with the keys 'host',
     *        'user', 'dbname' and 'password'.
     *        If an URL is used, it should follow the format
     *        'mysql://<user>:<password>@<host>/<dbname>'.
     *
     * @throws \InvalidArgumentException if the passed array does not contain the required fields
     *
     * @return array Returns an array with the keys 'host', 'user', 'dbname' and 'password'.
     *         The latter may be null.
     */
    public static function normalize(array $connectionParams): array
    {
        if (empty($connectionParams['url'])) {
            $diff = array_diff_key(['user', 'dbname'], $connectionParams);

            if (\count($diff) > 0) {
                throw new \InvalidArgumentException(sprintf('The following keys are missing from the $connectionParams: ', implode(', ', $diff)));
            }

            return [
                'host' => $connectionParams['host'] ?? 'localhost',
                'user' => $connectionParams['user'],
                'dbname' => $connectionParams['dbname'],
                'password' => $connectionParams['password'] ?? null,
            ];
        }

        $urlParts = parse_url($connectionParams['url']);

        if (false === $urlParts) {
            throw new \InvalidArgumentException('The connection URL is not a valid URL that follows the format "<schema>://<user>:<password>@<host>/<dbname>');
        }

        return [
            'host' => $urlParts['host'] ?? 'localhost',
            'user' => $urlParts['user'],
            'dbname' => substr($urlParts['path'], 1),
            'password' => $urlParts['password'] ?? null,
        ];
    }
}
