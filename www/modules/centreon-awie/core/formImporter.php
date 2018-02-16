<?php
/**
 * Copyright 2018 Centreon
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!isset($oreon)) {
    exit();
}

require_once _CENTREON_PATH_ . '/www/modules/centreon-awie/centreon-awie.conf.php';
require_once _CENTREON_PATH_ . '/www/modules/centreon-awie/core/DB-Func.php';
require_once _CENTREON_PATH_ . '/www/class/centreon-clapi/centreonAPI.class.php';
require_once _CENTREON_PATH_ . '/www/class/centreon-clapi/centreonUtils.class.php';
require_once _CENTREON_PATH_ . "/lib/Centreon/Db/Manager/Manager.php";


define('_CLAPI_CLASS_', _CENTREON_PATH_ . '/www/class/centreon-clapi/');
define('_CLAPI_LIB_', _CENTREON_PATH_ . '/lib/');

set_include_path(implode(
    PATH_SEPARATOR,
    array(
        realpath(_CLAPI_LIB_),
        realpath(_CLAPI_CLASS_),
        get_include_path()
    )
));

$import = realpath(dirname(__FILE__));
// Smarty template Init
$path = _MODULE_PATH_ . "/core/template/";
$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);

if (!is_null($_POST['validate'])) {
    $uploaddir = '/usr/share/centreon/filesUpload/';
    $uploadfile = $uploaddir . basename($_FILES['clapiImport']['name']);

    /**
     * Upload file
     */
    if (move_uploaded_file($_FILES['clapiImport']['tmp_name'], $uploadfile)) {
        echo "Le fichier est valide, et a été téléchargé
           avec succès. Voici plus d'informations :\n";
    } else {
        echo "Attaque potentielle par téléchargement de fichiers.
          Voici plus d'informations :\n";
    }

    /**
     * Dezippe file
     */
    $zip = new ZipArchive;
    $confPath = '/usr/share/centreon/filesUpload/';

    if ($zip->open($uploadfile) === true) {
        $zip->extractTo($confPath);
        $zip->close();
    } else {
        if ($zip->open($uploadfile) === false) {
            echo'Ça marche pas';
        }
    }

    /**
     * DB
     */
    $dbConfig['host'] = $conf_centreon['hostCentreon'];
    $dbConfig['username'] = $conf_centreon['user'];
    $dbConfig['password'] = $conf_centreon['password'];
    $dbConfig['dbname'] = $conf_centreon['db'];
    if (isset($conf_centreon['port'])) {
        $dbConfig['port'] = $conf_centreon['port'];
    } elseif ($p = strstr($dbConfig['host'], ':')) {
        $p = substr($p, 1);
        if (is_numeric($p)) {
            $dbConfig['port'] = $p;
        }
    }
    $db = Centreon_Db_Manager::factory('centreon', 'pdo_mysql', $dbConfig);
    $db->query('SET NAMES utf8');
    $dbConfig['dbname'] = $conf_centreon['dbcstg'];
    $db_storage = Centreon_Db_Manager::factory('storage', 'pdo_mysql', $dbConfig);
    try {
        $db->getConnection();
        $db_storage->getConnection();
    } catch (Exception $e) {
        echo sprintf("Could not connect to database. Check your configuration file %s\n", _CENTREON_ETC_.'/centreon.conf.php');
        if (isset($options['h'])) {
            CentreonClapi\CentreonAPI::printHelp(false, 1);
        }
        exit(1);
    }

    /**
     * Set log_contact
     */
    $username = $centreon->user->alias;
    CentreonClapi\CentreonUtils::setUserName($username);

    /**
     * Using CLAPI command to import configuration
     * Exemple -> "./centreon -u admin -p centreon -i /tmp/clapi-export.txt"
     */
    $finalFile = $confPath . basename($uploadfile, '.zip');
    $clapiObj = new \CentreonClapi\CentreonAPI('','','','','','');
    $clapiObj->import($finalFile);
}

$tpl->display($import . "/templates/formImport.tpl");