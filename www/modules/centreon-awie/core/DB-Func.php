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


require_once _CENTREON_PATH_ . '/www/modules/centreon-awie/class/Export.class.php';

function ExportFile($data)
{
    //echo '<pre>';
    $oExport = new Export();
    foreach ($data as $object => $value) {
        $type = explode('_', $object);
        if ($type[0] == 'export') {
            $oExport->ExportGroup($type[1], $value);
        } else {
            $oExport->ExportObject($type[0]);
        }
    }

    $oExport->ExportByClapi();
    readfile('/tmp/toto.zip');
    exit;
}
