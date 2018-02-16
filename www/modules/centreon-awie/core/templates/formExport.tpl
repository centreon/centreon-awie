<script type='text/javascript' src="./modules/centreon-awie/core/js/Export.js"></script>

<form name="exportForm" id="exportForm" enctype="multipart/form-data">
    <table id="exportTab" class="formTable table">
        <tr class="list_lvl_1">
            <td class="ListColLvl1_name" colspan="2">
                <h4>Api Web Exporter</h4>
            </td>
        </tr>
        <tr class="list_one">

            <td class="FormRowField">
                <img class="helpTooltip" name="export_options" >
                Export options;
            </td>

            <td class="FormRowValue">
                <input name="export_cmd[c_cmd]" type="checkbox" id="c_cmd"/>
                <label for="c_cmd">Check CMD</label>
                <input name="export_cmd[n_cmd]" type="checkbox" id="n_cmd"/>
                <label for="n_cmd">Notification CMD</label>
                <input name="export_cmd[m_cmd]" type="checkbox" id="m_cmd"/>
                <label for="m_cmd">Misc CMD</label>
                <input name="export_cmd[d_cmd]" type="checkbox" id="d_cmd"/>
                <label for="d_cmd">Discovery CMD</label>
            </td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField"></td>
            <td class="FormRowValue">
                <input name="TP" type="checkbox" id="tp"/>
                <label for="tp">Timeperiods</label>
                <input name="CONTACT" type="checkbox" id="contact"/>
                <label for="contact">Contacts</label>
                <input name="CG" type="checkbox" id="cgroup"/>
                <label for="cgroup">Contactgroups</label>
            </td>
        </tr>
        <tr class="list_two">
            <td class="FormRowField"></td>
            <td class="FormRowValue">
                <input onclick="selectFilter('HOST');" name="export_HOST[HOST]" type="checkbox" id="host"/>
                <label for="host">Hosts</label>
                <input style="display:none" placeholder="filter" name="export_HOST[HOST_filter]" type="txt"/>
            </td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField"></td>
            <td class="FormRowValue">
                <input onclick="selectFilter('HTPL');" name="export_HTPL[HTPL]" type="checkbox" id="htpl"/>
                <label for="htpl">HTPL</label>
                <input style="display:none" placeholder="filter" name="export_HTPL[HTPL_filter]" type="txt"/>
            </td>
        </tr>
        <tr class="list_two">
            <td class="FormRowField"></td>
            <td class="FormRowValue">
                <input name="HC" type="checkbox" id="host_c"/>
                <label for="host_c">Host Categories</label>
            </td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField"></td>
            <td class="FormRowValue">
                <input onclick="selectFilter('SERVICE');" name="export_SERVICE[SERVICE]" type="checkbox" id="svc"/>
                <label for="svc">Services</label>
                <input style="display:none" placeholder="filter" name="export_SERVICE[SERVICE_filter]" type="txt"/>
            </td>
        </tr>
        <tr class="list_two">
            <td class="FormRowField"></td>
            <td class="FormRowValue">
                <input onclick="selectFilter('STPL');" name="export_STPL[STPL]" type="checkbox" id="stpl"/>
                <label for="stpl">STPL</label>
                <input style="display:none" placeholder="filter" name="export_STPL[STPL_filter]" type="txt"/>
            </td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField"></td>
            <td class="FormRowValue">
                <input name="SC" type="checkbox" id="svc_c"/>
                <label for="svc_c">Service Categories</label></td>
        </tr>
        <tr class="list_two">
            <td class="FormRowField"></td>
            <td class="FormRowValue">
                <input name="ACL" type="checkbox" id="acl"/>
                <label for="acl">ACL</label>
                <input name="LDAP" type="checkbox" id="ldap"/>
                <label for="ldap">LDAP</label>
            </td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField"></td>
            <td class="FormRowValue">
                <input onclick="selectFilter('INSTANCE');" name="export_INSTANCE[INSTANCE]" type="checkbox" id="poller"/>
                <label for="poller">Poller</label>
                <input style="display:none" placeholder="filter" name="export_INSTANCE[INSTANCE_filter]" type="txt"/></td>
        </tr>
    </table>

    <div id="validForm">
        <p><input onclick="submitForm();" class="btc bt_success" name="submitC" value="Export" type="button" /></p>
    </div>
</form>

<form name="downloadForm" id="downloadForm" method="post" action="{$formPath}" enctype="multipart/form-data">
    <input name="pathFile" id="pathFile" type="hidden" />
</form>
