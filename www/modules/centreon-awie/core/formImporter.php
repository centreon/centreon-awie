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

$import = realpath(dirname(__FILE__));
// Smarty template Init
$path = _MODULE_PATH_ . "/core/template/";
$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);

if (!is_null($_POST['validate'])) {
    $uploaddir = '/clapi/';
    $uploadfile = $uploaddir.basename($_FILES['clapiImport']['name']);

    /**
     * Upload du fichier
     */
    if (move_uploaded_file($_FILES['clapiImport']['tmp_name'], $uploadfile)) {
        echo "Le fichier est valide, et a été téléchargé
           avec succès. Voici plus d'informations :\n";
    } else {
        echo "Attaque potentielle par téléchargement de fichiers.
          Voici plus d'informations :\n";
    }

    /**
     * Dezippe du fichier
     */
    $zip = new ZipArchive;
    $confPath = '/usr/share/centreon/filesUpload';

    if ($zip->open($uploadfile) === true) {
        $zip->extractTo($confPath);
        $zip->close();
        echo 'ok';
    } else {
        if ($zip->open($uploadfile) === false) {
            throw new \Exception('Ça marche pas');
        }
    }

    /**
     * Utilisation de la commande d'import CLAPI
     * ./centreon -u admin -p centreon -i /tmp/clapi-export.txt
     */

    exec("centreon -u admin -p centreon -i /usr/share/centreon/filesUpload/clapi-export\ 2.txt");
    header('Location: '.'http://10.30.2.57/centreon/main.php?p=61202');
    exit();
}

/**
 * Logs
 */

$tpl->display($import . "/templates/formImport.tpl");