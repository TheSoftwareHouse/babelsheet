<?php

namespace GDriveTranslations;

use GDriveTranslations\Config\Config;
use GuzzleHttp\Psr7\Response;

class GDriveDownloader
{
    private static function getService($scope)
    {
        try {
            $service = GDrive::getService($scope);
        } catch (\Exception $e) {
            exit('Could not connect to gdrive: '.$e->getMessage());
        }

        return $service;
    }

    public static function download(Config $config)
    {
        $service = self::getService($config->accessType);
        /* @var Response $response */
        try {
            $response = $service->files->export($config->fileId, 'text/csv', ['alt' => 'media']);
            $content = $response->getBody()->getContents();
        } catch (\Exception $e) {
            exit('Could not download the spreadsheet: '.$e->getMessage());
        }
        self::saveCSVToFile($content);
    }

    public static function create($filename)
    {
        $service = self::getService(Config::ACCESS_DRIVE);

        $copiedFile = new \Google_Service_Drive_DriveFile();
        $copiedFile->setName($filename);

        try {
            $gFile = $service->files->copy('1AUAKxhuZyjYl4NdpQCLBcSZe2snKAOjcXArlHRIn_hM', $copiedFile);

            return $gFile;
        } catch (\Exception $e) {
            exit('Could not create spreadsheet: '.$e->getMessage());
        }
    }

    private static function saveCSVToFile($csvContent)
    {
        $file = fopen('data.csv', 'w');
        fwrite($file, $csvContent);
        fclose($file);
    }
}
