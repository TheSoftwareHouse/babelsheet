<?php

namespace GDriveTranslations\Config;

class Config
{
    const ACCESS_DRIVE = 'drive';
    const ACCESS_FILE = 'file';

    /** @var string */
    public $fileId;
    /** @var Target[] */
    public $targets;
    /** @var string */
    public $accessType;
    /** @var string */
    public $sheetName;

    public function __construct($fileId, $accessType = self::ACCESS_DRIVE)
    {
        $this->fileId = $fileId;
        $this->targets = [];
        $this->accessType = $accessType;
    }
}
