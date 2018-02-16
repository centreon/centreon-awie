<?php
/*
 * Copyright 2005-2018 Centreon
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give Centreon
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of Centreon choice, provided that
 * Centreon also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 */

/**
 * Class Export
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
     * @param $clapiConnector
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
                return $this->GenerateObject($object);
            } elseif (!empty($value[$object . '_filter'])) {
                $filter = ';' . $value[$object . '_filter'];
                return $this->GenerateObject($object, $filter);
            }
        }
    }

    /**
     * @param $object
     * @param string $filter
     * @return string
     */
    public function GenerateObject(
        $object,
        $filter = ''
    ) {
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
     * @param $content
     * @return string
     */
    public function ClapiExport(
        $content
    ) {
        $fp = fopen($this->tmpFile, 'w');
        foreach ($content as $command) {
            fwrite($fp, utf8_encode($command));
        }
        fclose($fp);
        return $this->tmpName;
    }
}
