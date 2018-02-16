<script type='text/javascript' src="./modules/centreon-awie/core/js/Export.js"></script>



<form name="exportForm" id="exportForm" enctype="multipart/form-data">

    <div class="loadingWrapper" style="display: none">
        {include file='loading.tpl'}
    </div>

    <table id="exportTab" class="formTable table">
        <tr class="ListHeader">
            <td class="FormHeader">
                <h3>| Export objects:</h3>
            </td>
        </tr>
        <tr class="list_lvl_1">
            <td class="ListColLvl1_name" colspan="2">
                <h4>Pollers</h4>
            </td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField">Pollers</td>
            <td class="FormRowValue">
                <input onclick="selectFilter('INSTANCE');" name="export_INSTANCE[INSTANCE]" type="checkbox"
                       id="poller"/>
                <label for="poller">All</label>
                <span style="margin: 0 15px;vertical-align: middle;">or</span>
                <label for="poller1">Filter </label>
                <input type="text" id="poller1" placeholder="Ex: name" name="export_INSTANCE[INSTANCE_filter]" />
            </td>
        </tr>
        <tr class="list_lvl_1">
            <td class="ListColLvl1_name" colspan="2">
                <h4>Hosts</h4>
            </td>
        </tr>
        <tr class="list_two">
            <td class="FormRowField">Hosts</td>
            <td class="FormRowValue">
                <input onclick="selectFilter('HOST');" name="export_HOST[HOST]" type="checkbox" id="host"/>
                <label for="host">All</label>
                <span style="margin: 0 15px;vertical-align: middle;">or</span>
                <label for="host1">Filter </label>
                <input id="host1" placeholder="Ex: name" name="export_HOST[HOST_filter]" type="text"/>
            </td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField">Host templates</td>
            <td class="FormRowValue">
                <input onclick="selectFilter('HTPL');" name="export_HTPL[HTPL]" type="checkbox" id="htpl"/>
                <label for="htpl">All</label>
                <span style="margin: 0 15px;vertical-align: middle;">or</span>
                <label for="htpl1">Filter </label>
                <input id="htpl1" placeholder="Ex: name" name="export_HTPL[HTPL_filter]" type="text"/>
            </td>
        </tr>
        <tr class="list_two">
            <td class="FormRowField">Host categories</td>
            <td class="FormRowValue">
                <input name="HC" type="checkbox" id="host_c"/>
                <label for="host_c">Host Categories</label>
            </td>
        </tr>
        <tr class="list_lvl_1">
            <td class="ListColLvl1_name" colspan="2">
                <h4>Services</h4>
            </td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField">Services</td>
            <td class="FormRowValue">
                <input onclick="selectFilter('SERVICE');" name="export_SERVICE[SERVICE]" type="checkbox" id="svc"/>
                <label for="svc">All</label>
                <span style="margin: 0 15px;vertical-align: middle;">or</span>
                <label for="service1">Filter </label>
                <input id="service1" placeholder="Ex: name" name="export_SERVICE[SERVICE_filter]" type="text"/>
            </td>
        </tr>
        <tr class="list_two">
            <td class="FormRowField">Service templates</td>
            <td class="FormRowValue">
                <input onclick="selectFilter('STPL');" name="export_STPL[STPL]" type="checkbox" id="stpl"/>
                <label for="stpl">All</label>
                <span style="margin: 0 15px;vertical-align: middle;">or</span>
                <label for="stpl1">Filter </label>
                <input id="stpl1" placeholder="Ex: name" name="export_STPL[STPL_filter]" type="text"/>
            </td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField">Service categories</td>
            <td class="FormRowValue">
                <input name="SC" type="checkbox" id="svc_c"/>
                <label for="svc_c">Service Categories</label>
            </td>
        </tr>
        <tr class="list_lvl_1">
            <td class="ListColLvl1_name" >
                <h4>Contacts</h4>
            </td>
            <td class="FormRowValue" >
                <input name="CONTACT" type="checkbox" id="contact"/>
                <label for="contact">Contacts</label>
                <input name="CG" type="checkbox" id="cgroup"/>
                <label for="cgroup">Contactgroups</label>
            </td>
        </tr>
        <tr class="list_lvl_1">
            <td class="ListColLvl1_name" >
                <h4>Commands</h4>
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

        </tr>
        <tr class="list_lvl_1">
            <td class="ListColLvl1_name" >
                <h4>Resources</h4>
            </td>
            <td class="FormRowValue">
                <input name="ACL" type="checkbox" id="acl"/>
                <label for="acl">ACL</label>
                <input name="LDAP" type="checkbox" id="ldap"/>
                <label for="ldap">LDAP</label>
                <input name="TP" type="checkbox" id="tp"/>
                <label for="tp">Timeperiods</label>
            </td>
        </tr>
    </table>

    <div id="validForm">
        <p><input onclick="submitForm();" class="btc bt_success" name="submitC" value="Export" type="button"/></p>
    </div>


    {*<div class="waiting">*}
        {*<div class="loading-container">*}
            {*<svg id="icon-logo-centreon" width="42" height="42" viewBox="0 0 46 46" x="0" y="0">*}

                {*<path class= "color color-1" d="M41.3 5.6c-3.64-3.080-8.26-4.62-12.74-4.62v11.060c1.68 0 3.36 0.42 4.9 1.4v0c0.84 0.56 1.96 0.7 2.8-0.14l4.9-4.9c0.98-0.98 1.12-1.96 0.14-2.8z"></path>*}
                {*<path class= "color color-2" d="M22.26 14.7c1.68-1.68 4.060-2.66 6.3-2.66v-11.060c-5.18 0-10.22 1.96-14.14 5.88l7.84 7.84z"></path>*}
                {*<path class= "color color-3" d="M22.26 14.7l-7.84-7.84-13.72 12.46c-0.42 0.42-0.7 0.98-0.7 1.68h19.6c0.14-2.24 0.98-4.62 2.66-6.3z"></path>*}
                {*<path class= "color color-4" d="M14.42 35.14l7.84-7.84c-1.68-1.68-2.66-4.060-2.66-6.3h-19.6c0 0.7 0.28 1.26 0.7 1.68l13.72 12.46c0 0 0 0 0 0v0 0 0z"></path>*}
                {*<path class= "color color-5" d="M22.26 27.3v0l-7.84 7.84c3.92 3.92 8.96 5.88 14.14 5.88v-11.060c-2.24 0-4.48-0.84-6.3-2.66z"></path>*}
                {*<path class= "color color-6" d="M41.16 33.6l-5.040-5.040c-0.84-0.84-1.96-0.7-2.8-0.14v0c-1.4 0.98-3.22 1.4-4.9 1.4v11.060c4.48 0 9.1-1.54 12.74-4.62 1.12-0.7 0.98-1.68 0-2.66z"></path>*}
            {*</svg>*}
        {*</div>*}
    {*</div>*}




</form>

<form name="downloadForm" id="downloadForm" method="post" action="{$formPath}" enctype="multipart/form-data">
    <input name="pathFile" id="pathFile" type="hidden"/>
</form>
