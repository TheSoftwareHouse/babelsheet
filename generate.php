<?php

use GDriveTranslations\Config\Config;
use GDriveTranslations\GDriveDownloader;

require_once __DIR__.'/vendor/autoload.php';

date_default_timezone_set('Europe/Warsaw');

if ('cli' !== PHP_SAPI) {
    exit('This application must be run on the command line.');
}

const CONFIG_DIR = '/lang';

$configFilename = CONFIG_DIR . '/translate.json';
$credentialsFile = CONFIG_DIR . '/translate_token.json';

$GDriveServiceFactory = new \GDriveTranslations\GDrive($credentialsFile);
$downloader = new GDriveDownloader($GDriveServiceFactory->getService(Config::ACCESS_DRIVE));

$generator = new \GDriveTranslations\Generator\Generator();

$generator->load(CONFIG_DIR . '/messages.en_US.xlf', 'en_US');

$csvContent = fopen('php://memory', 'rw');

$generator->generateCsv($csvContent, 'en_US');

rewind($csvContent);

$gFile = $downloader->createFromCsv('imported translations', $csvContent);

printf("Created translations spreadsheet\n Visit https://docs.google.com/spreadsheets/d/%s to see it\n", $gFile->id);
