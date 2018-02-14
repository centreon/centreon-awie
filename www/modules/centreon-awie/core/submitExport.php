<?php

require_once __DIR__ . '/../../../../config/centreon.config.php';
require_once _CENTREON_PATH_ . '/www/modules/centreon-awie/class/Export.class.php';
$oExport = new Export();
$oExport->zipFilesAndDownload($_POST['pathFile']);

exit;
