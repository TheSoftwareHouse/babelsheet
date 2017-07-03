#!/usr/bin/env php
<?php

set_time_limit(0);

require __DIR__.'/vendor/autoload.php';

use GDriveTranslations\Config\Config;
use GDriveTranslations\GDrive;
use GDriveTranslations\GDriveDownloader;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;

const DEFAULT_WORK_DIR = '/lang';

$input = new ArgvInput();
$workDir = $input->getParameterOption(array('--dir', '-d'), DEFAULT_WORK_DIR);

$credentialsFile = $workDir . '/translate_token.json';

$GDriveServiceFactory = new GDrive($credentialsFile);
$downloader = new GDriveDownloader($GDriveServiceFactory->getService(Config::ACCESS_DRIVE));

$application = new Application('Babelsheet');
$application->getDefinition()->addOption(new InputOption(
    '--dir',
    '-d',
    InputOption::VALUE_REQUIRED,
    'work directory',
    DEFAULT_WORK_DIR
));

$application->addCommands([
    new \GDriveTranslations\Command\ConvertCommand($downloader),
    new \GDriveTranslations\Command\InitializeCommand($downloader),
    new \GDriveTranslations\Command\TranslateCommand($downloader),
]);

$application->run();
