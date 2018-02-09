<?php
/**
 * CENTREON
 *
 * Source Copyright 2005-2015 CENTREON
 *
 * Unauthorized reproduction, copy and distribution
 * are not allowed.
 *
 * For more information : contact@centreon.com
 *
 */

require_once _CENTREON_PATH_ . "/config/centreon.config.php";

class Export
{
    protected $bash = array();
    protected $user = '';
    protected $pwd = '';
    protected $tmpFile = '/tmp/ExportTmp.txt';
    protected $db;

    /**
     * Export constructor.
     */
    public function __construct()
    {
        if (is_null($this->db)) {
            try {
                $this->db = new \PDO("mysql:dbname=pdo;host=" . hostCentreon . ";dbname=" . db, user, password,
                    array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                echo 'Connexion échouée : ' . $e->getMessage();
            }
        }
        $this->user = 'superadmin';
        $this->pwd = 'centreon';
    }

    /**
     * @param $type
     */
    public function ExportCmd($type)
    {
        $cmdTypeRelation = array(
            'n' => 1,
            'c' => 2,
            'm' => 3,
            'd' => 4
        );
        $query = 'SELECT `command_name` FROM `command`WHERE `command_type` =' . $cmdTypeRelation[$type];
        $res = $this->db->query($query);
        while ($row = $res->fetch()) {
            $this->bash[] = "centreon -u $this->user -p $this->pwd -e --select='CMD;" . $row['command_name'] . "'";
        }
    }

    /**
     * @param $object
     * @param $value
     */
    public function ExportGroup($object, $value)
    {
        if ($object == 'cmd') {
            foreach ($value as $cmdType => $val) {
                $type = explode('_', $cmdType);
                $this->ExportCmd($type[0]);
            }
        } else {
            if (isset($value[$object])) {
                $filter = '';
                if (empty($value[$object])) {
                    $filter = ';' . $value[$object];
                }
                $this->ExportObject($object, $filter);
            }
        }
    }

    /**
     * @param $object
     * @param string $filter
     */
    public function ExportObject($object, $filter = '')
    {
        $this->bash[] = "centreon -u $this->user -p $this->pwd -e --select='$object$filter'";
    }

    /**
     *
     */
    public function ExportByClapi()
    {
        foreach ($this->bash as $command) {
            exec($command, $output);
            foreach ($output as $line) {
                $this->tmpFile = '/tmp/ExportTmp.txt';
                file_put_contents($this->tmpFile, $line . "\n", FILE_APPEND);
            }
        }

        //   var_dump(file_get_contents($this->tmpFile));
        $this->zipFilesAndDownload($this->tmpFile, '/tmp/toto.zip');

    }


    function zipFilesAndDownload($fileNames, $archiveName)
    {
        //echo $file_path;die;
        $zip = new ZipArchive();
        //create the file and throw the error if unsuccessful
        if ($zip->open($archiveName, ZIPARCHIVE::CREATE) !== true) {
            exit("cannot open <$archiveName>\n");
        }

        $zip->addFile($fileNames);

        $zip->close();
        //then send the headers to force download the zip file


    //    $archiveName = '/usr/share/centreon/filesUpload/toto.zip';


      //  var_dump(file_exists($archiveName));

        header("Pragma: no-cache");
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Cache-Control: max-age=0");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=" . basename($archiveName));
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($archiveName));
        header('Connection: close');
        readfile($archiveName);


//        header('Pragma: public'); 	// required
//        header('Expires: 0');		// no cache
//        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//        header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($archiveName)).' GMT');
//        header('Cache-Control: private',false);
//        header('Content-Type: application/zip');
//        header('Content-Disposition: attachment; filename="'.basename($archiveName).'"');
//        header('Content-Transfer-Encoding: binary');
//        header('Content-Length: '.filesize($archiveName));	// provide file size
//        header('Connection: close');
//        readfile($archiveName);		// push it out
//
        exit();

    }
















}
