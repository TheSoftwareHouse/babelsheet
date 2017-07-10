<?php

namespace GDriveTranslations;

use GDriveTranslations\Config\Config;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class GDriveDownloader
{
    /**
     * @var \Google_Service_Drive
     */
    private $drive;

    /**
     * @var \Google_Service_Sheets
     */
    private $sheets;

    public function __construct(\Google_Client $client)
    {
        $this->drive = new \Google_Service_Drive($client);
        $this->sheets = new \Google_Service_Sheets($client);
    }

    public function download(Config $config)
    {
        /* @var Response $response */
        try {

            $spreadsheet = $this->sheets->spreadsheets->get($config->fileId, ['includeGridData' => false]);

            $gid = '0';

            /** @var \Google_Service_Sheets_Sheet $sheet */
            foreach ($spreadsheet->getSheets() as $sheet) {
                if ($sheet->getProperties()->getTitle() === $config->sheetName) {
                    $gid = $sheet->getProperties()->getSheetId();
                }
            }

            $request = new Request(
                'GET',
                sprintf(
                    'https://docs.google.com/spreadsheets/d/%s/export?exportFormat=%s&gid=%s',
                    $config->fileId,
                    'csv',
                    $gid
                )
            );

            /** @var ResponseInterface $response */
            $response = $this->drive->getClient()->execute($request);

            $content = (string) $response->getBody();
        } catch (\Exception $e) {
            exit('Could not download the spreadsheet: '.$e->getMessage());
        }

        return $content;
    }

    public function create($filename)
    {
        $copiedFile = new \Google_Service_Drive_DriveFile();
        $copiedFile->setName($filename);

        try {
            $gFile = $this->drive->files->copy('1AUAKxhuZyjYl4NdpQCLBcSZe2snKAOjcXArlHRIn_hM', $copiedFile);

            return $gFile;
        } catch (\Exception $e) {
            exit('Could not create spreadsheet: '.$e->getMessage());
        }
    }
}
