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

class ImportCommand extends Command
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
    private $importDirectory;
    private $uploadsDirectory;

    public function __construct(
        string $databaseHost,
        string $databaseName,
        string $databaseUser,
        string $databasePassword,
        string $importDirectory,
        string $uploadsDirectory
    ) {
        parent::__construct();
        $this->databaseHost = $databaseHost;
        $this->databaseUser = $databaseUser;
        $this->databaseName = $databaseName;
        $this->databasePassword = $databasePassword;
        $this->importDirectory = $importDirectory;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    protected function configure()
    {
        $this
            ->setName("sulu:import")
            ->setDescription("Imports contents exported with the sulu:export command from the remote host.")
            ->addOption(
                "add-assets",
                null,
                null,
                "Add assets."
            );
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $skipAssets = $this->input->getOption("add-assets");
        $this->progressBar = new ProgressBar($this->output, $skipAssets ? 4 : 6);
        $this->progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% <info>%message%</info>');
        $this->importPHPCR();
        $this->importDatabase();
        if ($skipAssets) {
            $this->importUploads();
        }
        $this->progressBar->finish();
        $this->output->writeln(
            PHP_EOL . "<info>Successfully imported contents. You're good to go!</info>"
        );
    }

    private function importPHPCR()
    {
        $this->progressBar->setMessage("Importing PHPCR repository...");
        $this->executeCommand(
            "doctrine:phpcr:workspace:purge",
            [
                "--force" => true,
            ],
            new NullOutput()
        );
        $this->executeCommand(
            "doctrine:phpcr:workspace:import",
            [
                "filename" => $this->importDirectory . DIRECTORY_SEPARATOR . self::FILENAME_PHPCR
            ],
            new NullOutput()
        );
        $this->progressBar->advance();
    }
    private function importDatabase()
    {
        $this->progressBar->setMessage("Importing database...");
        $command =
            "mysql -h {$this->databaseHost} -u " . escapeshellarg($this->databaseUser) .
            ($this->databasePassword ? " -p" . escapeshellarg($this->databasePassword) : "") .
            " " . escapeshellarg($this->databaseName) . " < " . $this->importDirectory . DIRECTORY_SEPARATOR . self::FILENAME_SQL;
        $process = new Process($command);
        $process->run();
        $this->progressBar->advance();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
    private function importUploads()
    {
        $this->progressBar->setMessage("Importing uploads...");
        $filename = $this->importDirectory . DIRECTORY_SEPARATOR . self::FILENAME_UPLOADS;
        $path = $this->uploadsDirectory . DIRECTORY_SEPARATOR;
        $process = new Process("tar -xvf {$filename} {$path}");
        $process->run();
        $this->progressBar->advance();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
    private function executeCommand($cmd, array $params, OutputInterface $output)
    {
        $command = $this->getApplication()->find($cmd);
        $command->run(
            new ArrayInput(
                ["command" => $cmd] + $params
            ),
            $output
        );
    }

}