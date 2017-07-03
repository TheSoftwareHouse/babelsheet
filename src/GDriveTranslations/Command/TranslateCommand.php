<?php

namespace GDriveTranslations\Command;

use GDriveTranslations\GDriveDownloader;
use GDriveTranslations\Config\Reader;
use GDriveTranslations\Source\TranslationDataLoader;
use GDriveTranslations\Translator\Generator\AndroidGenerator;
use GDriveTranslations\Translator\Generator\iOSGenerator;
use GDriveTranslations\Translator\Generator\JsonGenerator;
use GDriveTranslations\Translator\Generator\XlfGenerator;
use GDriveTranslations\Translator\Translator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class TranslateCommand extends Command
{
    private $downloader;
    private $configReader;

    public function __construct(GDriveDownloader $GDriveDownloader)
    {
        parent::__construct();
        $this->downloader = $GDriveDownloader;
        $this->configReader = new Reader();
    }

    protected function configure()
    {
        $this
            ->setName('translate')
            ->setDescription('Converts spreadsheet translation to configured translation files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $workDir = $input->getOption('dir');

        $configFile = $workDir . DIRECTORY_SEPARATOR . 'translate.json';

        if (!file_exists($configFile)) {
            $question = new Question('translate.json do not exists. Enter spreadsheet name to initialize [empty to cancel]', false);
            $spreadsheetName = $this->getHelper('question')->ask($input, $output, $question);

            if (!$spreadsheetName) {
                $output->writeln('Cancelled');
                return;
            }

            $initializeCommand = $this->getApplication()->find('initialize');
            $arguments = [
                'command' => 'initialize',
                '--dir' => $workDir,
                'spreadsheet' => $spreadsheetName
            ];

            return $initializeCommand->run(new ArrayInput($arguments), $output);
        }

        $config = $this->configReader->read($configFile);

        $csvContent = $this->downloader->download($config);

        $file = fopen('data.csv', 'w');
        fwrite($file, $csvContent);
        fclose($file);

        $dataLoader = new TranslationDataLoader();
        $data = $dataLoader->load('data.csv');

        $translator = new Translator();
        $translator
            ->addGenerator(new JsonGenerator($workDir))
            ->addGenerator(new XlfGenerator($workDir))
            ->addGenerator(new iOSGenerator($workDir))
            ->addGenerator(new AndroidGenerator($workDir))
            ->generate($data, $config);

        $output->writeln('Translation file(s) generated.');
    }
}
