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

namespace TheCadien\Bundle\SuluImportExportBundle\Service;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use TheCadien\Bundle\SuluImportExportBundle\Helper\ImportExportDefaultMap;

class ImportService implements ImportInterface
{
    private $databaseHost;
    private $databaseUser;
    private $databaseName;
    private $databasePassword;
    private $importDirectory;
    private $uploadsDirectory;

    /**
     * @var ExecuteService
     */
    private $executeService;

    /**
     * ImportCommand constructor.
     */
    public function __construct(
        string $databaseHost,
        string $databaseName,
        string $databaseUser,
        string $databasePassword,
        string $importDirectory,
        string $uploadsDirectory,
        ExecuteService $executeService
    ) {
        $this->databaseHost = $databaseHost;
        $this->databaseUser = $databaseUser;
        $this->databaseName = $databaseName;
        $this->databasePassword = $databasePassword;
        $this->importDirectory = $importDirectory;
        $this->executeService = $executeService;
        $this->uploadsDirectory = ($uploadsDirectory) ?: ImportExportDefaultMap::SULU_DEFAULT_MEDIA_PATH;
    }

    public function importPHPCR()
    {
        $this->executeService->executeCommand(
            'doctrine:phpcr:workspace:purge',
            [
                '--force' => true,
            ],
            new NullOutput()
        );
        $this->executeService->executeCommand(
            'doctrine:phpcr:workspace:import',
            [
                'filename' => $this->importDirectory . \DIRECTORY_SEPARATOR . ImportExportDefaultMap::FILENAME_PHPCR,
            ],
            new NullOutput()
        );
    }

    public function importDatabase()
    {
        $command =
            "mysql -h {$this->databaseHost} -u " . escapeshellarg($this->databaseUser) .
            ($this->databasePassword ? ' -p' . escapeshellarg($this->databasePassword) : '') .
            ' ' . escapeshellarg($this->databaseName) . ' < ' . $this->importDirectory . \DIRECTORY_SEPARATOR . ImportExportDefaultMap::FILENAME_SQL;
        $process = Process::fromShellCommandline($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    public function importUploads()
    {
        $filename = $this->importDirectory . \DIRECTORY_SEPARATOR . ImportExportDefaultMap::FILENAME_UPLOADS;
        $path = $this->uploadsDirectory . \DIRECTORY_SEPARATOR;
        $process = Process::fromShellCommandline("tar -xvf {$filename} {$path}");
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
