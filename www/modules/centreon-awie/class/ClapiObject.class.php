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


class ClapiObject
{

    protected $dbConfigCentreon = array();
    protected $dbConfigCentreonStorage = array();
    protected $db;
    protected $dbMon;
    protected $clapiParameters = array();
    protected $clapiConnector;

    public function __construct($dbConfig, $clapiParameters)
    {
        $this->clapiParameters = $clapiParameters;
        $this->buildDatabaseConfigurations($dbConfig);
        $this->initCentreonConnection();
        $this->initCentreonStorageConnection();
        $this->connectToClapi();

    }

    /**
     * @param $dbConfig
     */
    private function buildDatabaseConfigurations($dbConfig)
    {
        $this->dbConfigCentreon = $dbConfig;
        $dbConfig['dbname'] = $dbConfig['storage'];
        $this->dbConfigCentreonStorage = $dbConfig;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addClapiParameter($key, $value)
    {
        $this->clapiParameters[$key] = $value;
    }

    /**
     *
     */
    private function initCentreonConnection()
    {
        $this->db = Centreon_Db_Manager::factory('centreon', 'pdo_mysql', $this->dbConfigCentreon);
        $this->testDatabaseConnection('db');
    }

    /**
     *
     */
    private function initCentreonStorageConnection()
    {
        $this->dbMon = Centreon_Db_Manager::factory('storage', 'pdo_mysql', $this->dbConfigCentreonStorage);
        $this->testDatabaseConnection('dbMon');
    }

    /**
     * @param $dbName
     */
    private function testDatabaseConnection($dbName)
    {
        try {
            $this->$dbName->getConnection();
        } catch (Exception $e) {
            echo sprintf("Could not connect to database. Check your configuration file %s\n",
                _CENTREON_ETC_ . '/centreon.conf.php');
        }
    }

    /**
     *
     */
    private function connectToClapi()
    {
        \CentreonClapi\CentreonUtils::setUserName($this->clapiParameters['username']);
        $this->clapiConnector = \CentreonClapi\CentreonAPI::getInstance(
            '',
            '',
            '',
            _CENTREON_PATH_,
            $this->clapiParameters
        );
    }

    /**
     * @return mixed
     */
    public function export()
    {
        $this->clapiConnector->setOption($this->clapiParameters);
        $export = $this->clapiConnector->export();
        return $export;
    }

}
