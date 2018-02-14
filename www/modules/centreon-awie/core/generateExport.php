<?php

require_once __DIR__ . '/../../../../config/centreon.config.php';
require_once _CENTREON_PATH_ . '/www/modules/centreon-awie/class/Export.class.php';
$oExport = new Export();
foreach ($_POST as $object => $value) {
    $type = explode('_', $object);
    if ($type[0] == 'export') {
        $oExport->GenerateGroup($type[1], $value);
    } elseif ( $type[0] !='submitC') {
        $oExport->GenerateObject($type[0]);
    }
}

$error = $oExport->ClapiExport();
echo json_encode($error);
exit;
