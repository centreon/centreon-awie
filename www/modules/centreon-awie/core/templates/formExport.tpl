<form {$form.attributes}>
    <table class="formTable table">
        <tr class="list_lvl_1">
            <td class="ListColLvl1_name" colspan="2">
                <h4>{$form.header.title}</h4>
            </td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField"><img class="helpTooltip" name="notification_options"> {$form.export_all.label}
            </td>
            <td class="FormRowValue">{$form.export_cmd.html}</td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField"></td>
            <td class="FormRowValue">{$form.tp.html} {$form.c.html} {$form.cg.html}</td>
        </tr>
        <tr class="list_two">
            <td class="FormRowField"></td>
            <td class="FormRowValue">{$form.export_host.html}</td>
        </tr>

        <tr class="list_one">
            <td class="FormRowField"></td>
            <td class="FormRowValue">{$form.export_htpl.html}</td>
        </tr>
        <tr class="list_two">
            <td class="FormRowField"></td>
            <td class="FormRowValue">{$form.host_c.html}</td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField"></td>
            <td class="FormRowValue">{$form.export_svc.html}</td>
        </tr>
        <tr class="list_two">
            <td class="FormRowField"></td>
            <td class="FormRowValue">{$form.export_stpl.html}</td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField"></td>
            <td class="FormRowValue">{$form.svc_c.html}</td>
        </tr>
        <tr class="list_two">
            <td class="FormRowField"></td>
            <td class="FormRowValue">{$form.acl.html} {$form.ldap.html}</td>
        </tr>
        <tr class="list_one">
            <td class="FormRowField"></td>
            <td class="FormRowValue">{$form.export_poller.html}</td>
        </tr>
    </table>

    {if !$valid}
        <div id="validForm">
            <p>{$form.submitC.html}</p>
        </div>
    {else}
        <div id="validForm">
            <p>{$form.change.html}</p>
        </div>
    {/if}

    {$form.hidden}
</form>
{$helpText}
{literal}
    <script type='text/javascript'>
        function selectFilter(selected) {
            if (jQuery('[name = "export_' + selected + '[' + selected +'_filter]"]').css('display') == 'none') {
                jQuery('[name = "export_' + selected + '[' + selected +'_filter]"]').css('display', 'block');
            } else {
                jQuery('[name = "export_' + selected + '[' + selected +'_filter]"]').css('display', 'none');
            }
        }
    </script>
{/literal}