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

require_once dirname(__FILE__) . '/../../../../config/centreon.config.php';
require_once _CENTREON_PATH_ . '/www/modules/centreon-awie/class/Export.class.php';
require_once _CENTREON_PATH_ . '/www/modules/centreon-awie/class/ClapiObject.class.php';
require_once _CENTREON_PATH_ . '/www/class/centreon.class.php';
require_once _CENTREON_PATH_ . '/www/class/centreonUser.class.php';
require_once _CENTREON_PATH_ . '/www/class/centreonSession.class.php';
require_once _CENTREON_PATH_ . '/www/modules/centreon-awie/centreon-awie.conf.php';

define('_CLAPI_LIB_', _CENTREON_PATH_ . "/lib");
define('_CLAPI_CLASS_', _CENTREON_PATH_ . "/www/class/centreon-clapi");

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(_CLAPI_LIB_),
    realpath(_CLAPI_CLASS_),
    get_include_path()
)));
require_once _CLAPI_LIB_ . "/Centreon/Db/Manager/Manager.php";
require_once _CLAPI_CLASS_ . "/centreonUtils.class.php";
require_once _CLAPI_CLASS_ . "/centreonAPI.class.php";


$formValue = array(
    'export_cmd',
    'TP',
    'CONTACT',
    'CG',
    'export_HOST',
    'export_HTPL',
    'HC',
    'export_SERVICE',
    'export_STPL',
    'SC',
    'ACL',
    'LDAP',
    'export_INSTANCE'
);

$dbConfig['host'] = $conf_centreon['hostCentreon'];
$dbConfig['username'] = $conf_centreon['user'];
$dbConfig['password'] = $conf_centreon['password'];
$dbConfig['dbname'] = $conf_centreon['db'];
$dbConfig['storage'] = $conf_centreon['dbcstg'];
if (isset($conf_centreon['port'])) {
    $dbConfig['port'] = $conf_centreon['port'];
} elseif ($p = strstr($dbConfig['host'], ':')) {
    $p = substr($p, 1);
    if (is_numeric($p)) {
        $dbConfig['port'] = $p;
    }
}

$centreonSession = new CentreonSession();
$centreonSession->start();
$username = $_SESSION['centreon']->user->alias;
$clapiConnector = new \ClapiObject($dbConfig, array('username' => $username));

/*
* Set log_contact
*/
\CentreonClapi\CentreonUtils::setUserName($username);

$scriptContent = array();
$ajaxReturn = array();

$oExport = new \Export($clapiConnector);

foreach ($_POST as $object => $value) {

    if(in_array($object, $formValue)){
        $type = explode('_', $object);
        if ($type[0] == 'export') {
            $scriptContent[] = $oExport->GenerateGroup($type[1], $value);
        } elseif ($type[0] != 'submitC') {
            $scriptContent[] = $oExport->GenerateObject($type[0]);
        }
    } else {
        $ajaxReturn['error'][] = 'Unknown object : ' . $object;
    }

}
$ajaxReturn['fileGenerate'] = $oExport->ClapiExport($scriptContent);
echo json_encode($ajaxReturn);
exit;
