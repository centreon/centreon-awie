<?php
/**
 * CENTREON
 *
 * Source Copyright 2005-2018 CENTREON
 *
 * Unauthorized reproduction, copy and distribution
 * are not allowed.
 *
 * For more information : contact@centreon.com
 *
 */

class ZipAndDownload
{
    /**
     * ZipAndDownload constructor.
     * @param $fileNames
     * @param string $filePath
     * @param string $fileExtension
     */
    public function __construct($fileNames, $filePath = '/tmp', $fileExtension = '.txt')
    {
        $archivePath = $filePath . '/' . $fileNames . '.zip';
        $filePath = $filePath . '/' . $fileNames . $fileExtension;
        //echo $file_path;die;
        $zip = new ZipArchive();
        //create the file and throw the error if unsuccessful
        if ($zip->open($archivePath, ZIPARCHIVE::CREATE) !== true) {
            exit("cannot open <$archivePath>\n");
        }

        $zip->addFile($filePath, basename($filePath));
        $zip->close();

        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename=' . basename($archivePath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($archivePath));
        readfile($archivePath);
    }
}
