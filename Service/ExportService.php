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

class ExportService implements ExportInterface
{
    private $databaseHost;
    private $databaseUser;
    private $databaseName;
    private $databasePassword;
    private $exportDirectory;
    private $uploadsDirectory;
    /**
     * @var ExecuteService
     */
    private $executeService;

    public function __construct(
        string $databaseHost,
        string $databaseName,
        string $databaseUser,
        string $databasePassword,
        string $exportDirectory,
        string $uploadsDirectory,
        ExecuteService $executeService
    ) {
        parent::__construct();
        $this->databaseHost = $databaseHost;
        $this->databaseUser = $databaseUser;
        $this->databaseName = $databaseName;
        $this->databasePassword = $databasePassword;
        $this->exportDirectory = $exportDirectory;
        $this->executeService = $executeService;
        $this->uploadsDirectory = ($uploadsDirectory) ?: ImportExportDefaultMap::SULU_DEFAULT_MEDIA_PATH;
    }

    public function exportPHPCR()
    {
        $this->executeService->executeCommand(
            'doctrine:phpcr:workspace:export',
            [
                '-p' => '/cmf',
                'filename' => $this->exportDirectory . \DIRECTORY_SEPARATOR . ImportExportDefaultMap::FILENAME_PHPCR,
            ],
            new NullOutput()
        );
    }

    public function exportDatabase()
    {
        $command =
            "mysqldump -h {$this->databaseHost} -u " . escapeshellarg($this->databaseUser) .
            ($this->databasePassword ? ' -p' . escapeshellarg($this->databasePassword) : '') .
            ' ' . escapeshellarg($this->databaseName) . ' > ' . $this->exportDirectory . \DIRECTORY_SEPARATOR . ImportExportDefaultMap::FILENAME_SQL;

        $process = Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    public function exportUploads()
    {
        // Directory path with new Symfony directory structure - i.e. var/uploads.
        $process = Process::fromShellCommandline(
            'tar cvf ' . $this->exportDirectory . \DIRECTORY_SEPARATOR . ImportExportDefaultMap::FILENAME_UPLOADS . " {$this->uploadsDirectory}"
        );
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
