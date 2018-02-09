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

if (!isset($oreon)) {
    exit();
}

require_once _CENTREON_PATH_ . '/www/modules/centreon-awie/centreon-awie.conf.php';
require_once _CENTREON_PATH_ . '/www/modules/centreon-awie/core/DB-Func.php';
require_once _CENTREON_PATH_ . '/www/lib/HTML/QuickForm.php';
require_once _CENTREON_PATH_ . '/www/lib/HTML/QuickForm/Renderer/ArraySmarty.php';
//require_once _MODULE_PATH_ . 'core/help.php';

$export = realpath(dirname(__FILE__));
// Smarty template Init
$path = _MODULE_PATH_ . "/core/template/";
$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);
$form = new HTML_QuickForm('Form', 'post', "?p=" . $p);

$valid = false;
if ($form->validate()) {
    $valid = true;
    $form->freeze();
}

$form->addElement('header', 'title', _("Api Web Exporter"));
//CMD
$exportCmd[] = HTML_QuickForm::createElement('checkbox', 'c_cmd', '&nbsp;', _("Check CMD"));
$exportCmd[] = HTML_QuickForm::createElement('checkbox', 'n_cmd', '&nbsp;', _("Notification CMD"));
$exportCmd[] = HTML_QuickForm::createElement('checkbox', 'm_cmd', '&nbsp;', _("Misc CMD"));
$exportCmd[] = HTML_QuickForm::createElement('checkbox', 'd_cmd', '&nbsp;', _("Discovery CMD"));
$form->addGroup($exportCmd, 'export_cmd', '', '&nbsp;');

//Contact
$form->addElement('checkbox', 'tp', '&nbsp;', _("Timeperiods"));
$form->addElement('checkbox', 'c', '', _("Contacts"));
$form->addElement('checkbox', 'cg', '', _("Contactgroups"));

//Host
$exportHost[] = HTML_QuickForm::createElement(
    'checkbox',
    'host',
    '&nbsp;',
    _("Host"),
    array("onclick" => "selectFilter('host');")
);
$exportHost[] = HTML_QuickForm::createElement('text', 'host_filter', '', array("style" => "display:none"));
$form->addGroup($exportHost, 'export_host', '', '&nbsp;');

$exportHtpl[] = HTML_QuickForm::createElement(
    'checkbox',
    'htpl',
    '&nbsp;',
    _("HTPL"),
    array("onclick" => "selectFilter('htpl');")
);
$exportHtpl[] = HTML_QuickForm::createElement('text', 'htpl_filter', '', array("style" => "display:none"));
$form->addGroup($exportHtpl, 'export_htpl', '', '&nbsp;');

$form->addElement('checkbox', 'host_c', '&nbsp;', _("Host Categories"));

//Service
$exportSvc[] = HTML_QuickForm::createElement('checkbox', 'host', '&nbsp;', _("Services"), array("onclick" => "selectFilter('svc');"));
$exportSvc[] = HTML_QuickForm::createElement('text', 'svc_filter', '', array("style" => "display:none"));
$form->addGroup($exportSvc, 'export_svc', '', '&nbsp;');

$exportStpl[] = HTML_QuickForm::createElement('checkbox', 'stpl', '&nbsp;', _("STPL"), array("onclick" => "selectFilter('stpl');"));
$exportStpl[] = HTML_QuickForm::createElement('text', 'stpl_filter', '', array("style" => "display:none"));
$form->addGroup($exportStpl, 'export_stpl', '', '&nbsp;');

$form->addElement('checkbox', 'svc_c', '&nbsp;', _("Service Categories"));


//Connexion
$form->addElement('checkbox', 'acl', '', _("ACL"));
$form->addElement('checkbox', 'ldap', '', _("LDAP"));

//Poller
$exportPoller[] = HTML_QuickForm::createElement('checkbox', 'poller', '&nbsp;', _("Poller"), array("onclick" => "selectFilter('poller');"));
$exportPoller[] = HTML_QuickForm::createElement('text', 'poller_filter', '', array("style" => "display:none"));
$form->addGroup($exportPoller, 'export_poller', '', '&nbsp;');

$subC = $form->addElement('submit', 'submitC', _("Export"), array("class" => "btc bt_success"));
$res = $form->addElement('reset', 'reset', _("Reset"));

if ($valid) {
    $form->freeze();
}

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($tpl, true);
$form->accept($renderer);
$tpl->assign('form', $renderer->toArray());
$tpl->assign('valid', $valid);
$tpl->display($export . "/templates/formExport.tpl");


$valid = false;
if ($form->validate()) {
    $valid = true;
    ExportFile($form->getSubmitValues());
}


