<?php

use GDriveTranslations\Config\Config;
use GDriveTranslations\GDrive;
use GDriveTranslations\GDriveDownloader;
use GDriveTranslations\Generator\Generator;

require_once __DIR__.'/vendor/autoload.php';

date_default_timezone_set('Europe/Warsaw');

if ('cli' !== PHP_SAPI) {
    exit('This application must be run on the command line.');
}

const CONFIG_DIR = '/lang';

$configFilename = CONFIG_DIR . '/translate.json';
$credentialsFile = CONFIG_DIR . '/translate_token.json';

$GDriveServiceFactory = new GDrive($credentialsFile);
$downloader = new GDriveDownloader($GDriveServiceFactory->getService(Config::ACCESS_DRIVE));
$generator = new Generator();

$filename = readline(sprintf('Enter translations filename inside %s directory:', CONFIG_DIR));
if ('' === $filename) {
    exit('Cancelled');
}
$locale = readline('Enter locale code:');
if ('' === $locale) {
    exit('Cancelled');
}

$generator->load(CONFIG_DIR . '/' . $filename, $locale);

$csvContent = fopen('php://memory', 'rw');

$generator->generateCsv($csvContent, $locale);

rewind($csvContent);

$gFileName = readline('Enter spreadhseet name');
if ('' === $gFileName) {
    $gFileName = 'imported translations';
}

$gFile = $downloader->createFromCsv($gFileName, $csvContent);

printf("Created translations spreadsheet\n Visit https://docs.google.com/spreadsheets/d/%s to see it\n", $gFile->id);
