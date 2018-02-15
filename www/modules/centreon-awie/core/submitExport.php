<?php

require_once __DIR__ . '/../../../../config/centreon.config.php';
require_once _CENTREON_PATH_ . '/www/modules/centreon-awie/class/ZipAndDownload.class.php';
$oExport = new \ZipAndDownload($_POST['pathFile']);
