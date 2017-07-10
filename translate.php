<?php

$downloadTime = -microtime(true);

use GDriveTranslations\Config\Config;
use GDriveTranslations\Config\Reader;
use GDriveTranslations\Config\Writer;
use GDriveTranslations\GDriveDownloader;
use GDriveTranslations\Source\TranslationDataLoader;
use GDriveTranslations\Translator\Translator;
use GDriveTranslations\Translator\Generator\AndroidGenerator;
use GDriveTranslations\Translator\Generator\iOSGenerator;
use GDriveTranslations\Translator\Generator\JsonGenerator;
use GDriveTranslations\Translator\Generator\XlfGenerator;

require_once __DIR__.'/vendor/autoload.php';

//error_reporting(0);
date_default_timezone_set('Europe/Warsaw');

echo "\nTranslations from GDrive\n========================\n\n";

if (php_sapi_name() != 'cli') {
    exit('This application must be run on the command line.');
}

const CONFIG_DIR = '/lang';

$configFilename = CONFIG_DIR . '/translate.json';
$credentialsFile = CONFIG_DIR . '/translate_token.json';

$GDriveServiceFactory = new \GDriveTranslations\GClientFactory($credentialsFile);
$downloader = new GDriveDownloader($GDriveServiceFactory->createClient(Config::ACCESS_DRIVE));

if (file_exists($configFilename)) {

    $configReader = new Reader();
    $config = $configReader->read($configFilename);

    $csvContent = $downloader->download($config);
    $parseTime = -microtime(true);
    $downloadTime += microtime(true);

    $file = fopen('data.csv', 'w');
    fwrite($file, $csvContent);
    fclose($file);

    $dataLoader = new TranslationDataLoader();
    $data = $dataLoader->load('data.csv');

    $translator = new Translator();
    $translator
        ->addGenerator(new JsonGenerator(CONFIG_DIR))
        ->addGenerator(new XlfGenerator(CONFIG_DIR))
        ->addGenerator(new iOSGenerator(CONFIG_DIR))
        ->addGenerator(new AndroidGenerator(CONFIG_DIR))
        ->generate($data, $config);

    $parseTime += microtime(true);
    echo 'took '.round($downloadTime * 1000).'ms. downloading, '.round($parseTime * 1000).'ms. parsing. Peak memory usage: '.round(memory_get_peak_usage(true) / 1024 / 1024, 2)."MB\n\n";
} else {
    $line = readline('No translate.json found. Enter spreadsheet name to initialize [empty to cancel] > ');
    echo "\n";
    if(!$line) {
        exit('Cancelled'."\n\n");
    }
    $sheetFile = $downloader->create($line);

    $config = new Config($sheetFile->id, Config::ACCESS_FILE);

    $writer = new Writer();
    $writer->write($config, $configFilename);

    printf("Translations spreadsheet initialized and available under https://docs.google.com/spreadsheets/d/%s\n", $sheetFile->id);
    echo "translate.json has been saved. \n";
    echo "Please edit translate.json to add targets.\n\n";
}
