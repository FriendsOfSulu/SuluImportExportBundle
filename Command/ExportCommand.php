<?php

namespace App\Bundles\SuluImportExportBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ExportCommand extends Command
{
    const FILENAME_PHPCR = 'export.phpcr';

    const FILENAME_SQL = 'export.sql';

    const FILENAME_UPLOADS = 'uploads.tar.gz';

    /**
     * @var InputInterface
     */
    private $input;
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var ProgressBar
     */
    private $progressBar;
    private $databaseHost;
    private $databaseUser;
    private $databaseName;
    private $databasePassword;
    private $exportDirectory;
    private $uploadsDirectory;

    public function __construct(
        string $databaseHost,
        string $databaseName,
        string $databaseUser,
        string $databasePassword,
        string $exportDirectory,
        string $uploadsDirectory
    ) {
        parent::__construct();
        $this->databaseHost = $databaseHost;
        $this->databaseUser = $databaseUser;
        $this->databaseName = $databaseName;
        $this->databasePassword = $databasePassword;
        $this->exportDirectory = $exportDirectory;
        $this->uploadsDirectory = $uploadsDirectory;
    }
    protected function configure()
    {
        $this
            ->setName("sulu:export")
            ->setDescription("Exports all Sulu contents (PHPCR, database, uploads) to the web directory.");
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->progressBar = new ProgressBar($this->output, 3);
        $this->progressBar->setFormat("%current%/%max% [%bar%] %percent:3s%% <info>%message%</info>");
        $this->exportPHPCR();
        $this->exportDatabase();
        $this->exportUploads();
        $this->progressBar->finish();
        $this->output->writeln(
            PHP_EOL . "<info>Successfully exported contents.</info>"
        );
    }

    private function exportPHPCR()
    {
        $this->progressBar->setMessage("Exporting PHPCR repository...");
        $this->executeCommand(
            "doctrine:phpcr:workspace:export",
            [
                "-p" => "/cmf",
                "filename" => $this->exportDirectory . DIRECTORY_SEPARATOR . self::FILENAME_PHPCR
            ]
        );
        $this->progressBar->advance();
    }

    private function exportDatabase()
    {
        $this->progressBar->setMessage("Exporting database...");
        $command =
            "mysqldump -h {$this->databaseHost} -u " . escapeshellarg($this->databaseUser) .
            ($this->databasePassword ? " -p" . escapeshellarg($this->databasePassword) : "") .
            " " . escapeshellarg($this->databaseName) . " > " . $this->exportDirectory . DIRECTORY_SEPARATOR . self::FILENAME_SQL;

        $process = new Process($command);
        $process->run();
        $this->progressBar->advance();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
    private function exportUploads()
    {
        $this->progressBar->setMessage("Exporting uploads...");
        // Directory path with new Symfony directory structure - i.e. var/uploads.
        $process = new Process(
            "tar cvf " . $this->exportDirectory . DIRECTORY_SEPARATOR  . self::FILENAME_UPLOADS . " {$this->uploadsDirectory}"
        );
        $process->setTimeout(300);
        $process->run();
        $this->progressBar->advance();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
    private function executeCommand($cmd, array $params)
    {
        $command = $this->getApplication()->find($cmd);
        $command->run(
            new ArrayInput(
                ["command" => $cmd] + $params
            ),
            new NullOutput()
        );
    }
}