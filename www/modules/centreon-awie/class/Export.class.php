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
    protected $tmpFile = '';
    protected $tmpName = '';
    protected $db;
    protected $outputValue = array();

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

        $this->tmpName = 'centreon-clapi-export-' . time();
        $this->tmpFile = '/tmp/' . $this->tmpName . '.txt';

    }

    /**
     * @param $type
     */
    public function GenerateCmd($type)
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
    public function GenerateGroup($object, $value)
    {
        if ($object == 'cmd') {
            foreach ($value as $cmdType => $val) {
                $type = explode('_', $cmdType);
                $this->GenerateCmd($type[0]);
            }
        } else {
            if (isset($value[$object])) {
                $filter = '';
                if (!empty($value[$object . '_filter'])) {
                    $filter = ';' . $value[$object . '_filter'];
                }
                $this->GenerateObject($object, $filter);
            }
        }
    }

    /**
     * @param $object
     * @param string $filter
     */
    public function GenerateObject($object, $filter = '')
    {
        if ($object == 'ACL') {
            $this->GenerateAcl();
        } else {
            $this->bash[] = "centreon -u $this->user -p $this->pwd -e --select='$object$filter'";
        }

    }

    /**
     *
     */
    public function GenerateAcl()
    {
        $oAcl = array('ACLMENU', 'ACLACTION', 'ACLRESOURCE', 'ACLGROUP');
        foreach ($oAcl as $acl) {
            $this->GenerateObject($acl);
        }
    }

    /**
     *
     */
    public function ClapiExport()
    {
        $fp = fopen($this->tmpFile, 'w');
        foreach ($this->bash as $command) {
            exec($command, $output, $error);
            if ($error == 1) {
                $this->outputValue[] = $output[0];
            } else {
                foreach ($output as $line) {
                    fwrite($fp, $line . "\n");
                }
            }
        }
        fclose($fp);
        // $archive = '/tmp/' . $this->tmpName . '.zip';
        $this->outputValue['fileGenerate'] = $this->tmpName;

        return $this->outputValue;
    }


    public function zipFilesAndDownload($fileNames)
    {
        $archivePath = '/tmp/' . $fileNames . '.zip';
        $filePath = '/tmp/' . $fileNames . '.txt';

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
