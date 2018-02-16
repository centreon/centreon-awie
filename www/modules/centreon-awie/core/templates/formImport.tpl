<script type='text/javascript' src="./modules/centreon-awie/core/js/Import.js"></script>

<form method="post" id="importForm" name="importForm" enctype="multipart/form-data">
    <table id="exportTab" class="formTable table">
        <tr class="ListHeader">
            <td class="FormHeader">
                <h3>| Import objects:</h3>
            </td>
        </tr>
        <tr class="list_lvl_1">
            <td class="ListColLvl1_name" colspan="2">
                <h4>Import zip archive <i style="color:red;" size="1">*</i></h4>
            </td>
            <td class="FormRowValue">
                <input onchange="checkSize(this);" type="file" id="file" name="clapiImport" required="required"/>
            </td>
        </tr>
    </table>

    <div id="validForm">
        {*<p><input class="btc bt_success" value="Import" type="submit"/></p>*}
        <p><input onclick="submitForm();" class="btc bt_success" value="Import" type="button"/></p>
    </div>
</form>

<div id="logClapi">

</div>