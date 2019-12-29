<?php

namespace GDriveTranslations\Command;

use GDriveTranslations\GDriveDownloader;
use GDriveTranslations\Generator\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertCommand extends Command
{
    /**
     * @var GDriveDownloader
     */
    private $downloader;
    /**
     * @var Generator
     */
    private $generator;


    public function __construct(GDriveDownloader $GDriveDownloader)
    {
        parent::__construct();
        $this->downloader = $GDriveDownloader;
        $this->generator = new Generator();
    }

    protected function configure()
    {
        $this
            ->setName('convert')
            ->setDescription('Converts existing Xliff file to babelsheet spreadsheet')
            ->addOption('locale', 'l',InputOption::VALUE_REQUIRED, 'Locale code')
            ->addArgument('filename', InputArgument::REQUIRED, 'Filename of translations in xliff format')
            ->addOption('spreadsheet', 's', InputOption::VALUE_OPTIONAL, 'Spreadsheet name', 'converted translations')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locale = $input->getOption('locale');
        $workDir = $input->getOption('dir');
        $spreadsheetName = $input->getOption('spreadsheet');
        $filename = $input->getArgument('filename');

        $this->generator->load($workDir . DIRECTORY_SEPARATOR . $filename, $locale);

        $csvContent = fopen('php://memory', 'rw');

        $this->generator->generateCsv($csvContent, $locale);

        rewind($csvContent);

        $gFile = $this->downloader->createFromCsv($spreadsheetName, $csvContent);

        $output->writeln([
            'Translations spreadsheet created.',
            sprintf('Visit https://docs.google.com/spreadsheets/d/%s to see it', $gFile->id)
        ]);
    }
}
