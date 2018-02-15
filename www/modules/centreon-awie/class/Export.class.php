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

class Export
{
    protected $bash = array();
    protected $user = '';
    protected $pwd = '';
    protected $tmpFile = '';
    protected $tmpName = '';
    protected $db;
    protected $clapiConnector;

    /**
     * Export constructor.
     */
    public function __construct($clapiConnector)
    {
        $this->db = new \CentreonDB();
        $this->clapiConnector = $clapiConnector;
        $this->tmpName = 'centreon-clapi-export-' . time();
        $this->tmpFile = '/tmp/' . $this->tmpName . '.txt';
    }

    /**
     * @param $type
     * @return string
     */
    private function GenerateCmd($type)
    {
        $cmdScript = '';
        $cmdTypeRelation = array(
            'n' => 1,
            'c' => 2,
            'm' => 3,
            'd' => 4
        );
        $query = 'SELECT `command_name` FROM `command`WHERE `command_type` =' . $cmdTypeRelation[$type];
        $res = $this->db->query($query);

        while ($row = $res->fetchRow()) {
            $cmdScript .= $this->GenerateObject('CMD', ';' . $row['command_name']);
        }
        return $cmdScript;
    }

    /**
     * @param $object
     * @param $value
     * @return string
     */
    public function GenerateGroup($object, $value)
    {
        if ($object == 'cmd') {
            foreach ($value as $cmdType => $val) {
                $type = explode('_', $cmdType);
                return $this->GenerateCmd($type[0]);
            }
        } else {
            if (isset($value[$object])) {
                $filter = '';
                if (!empty($value[$object . '_filter'])) {
                    $filter = ';' . $value[$object . '_filter'];
                }
                return $this->GenerateObject($object, $filter);
            }
        }
    }

    /**
     * @param $object
     * @param string $filter
     * @return string
     */
    public function GenerateObject($object, $filter = '')
    {
        $content = '';
        if ($object == 'ACL') {
            $this->GenerateAcl();
        } else {
            ob_start();
            $option = $object . $filter;
            $this->clapiConnector->addClapiParameter('select', $option);
            $this->clapiConnector->export();
            $content .= ob_get_contents();
            ob_end_clean();
        }
        return $content;
    }

    /**
     * @return string
     */
    private function GenerateAcl()
    {
        $aclScript = '';
        $oAcl = array('ACLMENU', 'ACLACTION', 'ACLRESOURCE', 'ACLGROUP');
        foreach ($oAcl as $acl) {
            $aclScript .= $this->GenerateObject($acl);
        }
        return $aclScript;
    }

    /**
     *
     */
    public function ClapiExport($content)
    {
        $fp = fopen($this->tmpFile, 'w');
        foreach ($content as $command) {
            fwrite($fp, utf8_encode($command));
        }
        fclose($fp);
        return $this->tmpName;
    }
}
