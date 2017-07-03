<?php

namespace GDriveTranslations\Command;

use GDriveTranslations\GDriveDownloader;
use GDriveTranslations\Config\Writer;
use GDriveTranslations\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class InitializeCommand extends Command
{
    private $downloader;
    private $configWriter;

    public function __construct(GDriveDownloader $GDriveDownloader)
    {
        parent::__construct();
        $this->downloader = $GDriveDownloader;
        $this->configWriter = new Writer();
    }

    protected function configure()
    {
        $this
            ->setName('initialize')
            ->setDescription('Initializes translate.json config file and creates translations spreadsheet')
            ->addArgument('spreadsheet', InputArgument::REQUIRED, 'Spreadsheet name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $workDir = $input->getOption('dir');
        $spreadsheetName = $input->getArgument('spreadsheet');

        $configFile = $workDir . DIRECTORY_SEPARATOR . 'translate.json';

        if (file_exists($configFile)) {
            $question = new ConfirmationQuestion('translate.json exists. Do you want to override it?', false);

            if (!$this->getHelper('question')->ask($input, $output, $question)) {
                return;
            }
        }

        $sheetFile = $this->downloader->createFromExample($spreadsheetName);

        $config = new Config($sheetFile->id);

        $this->configWriter->write($config, $configFile);

        $output->writeln([
            'Translations spreadsheet initialized.',
            sprintf('Visit https://docs.google.com/spreadsheets/d/%s to see it', $sheetFile->id),
            'translate.json file created. Edit it to add targets.'
        ]);
    }
}
