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
