function selectFilter(selected) {
    if (jQuery('[name = "export_' + selected + '[' + selected + '_filter]"]').css('display') == 'none') {
        jQuery('[name = "export_' + selected + '[' + selected + '_filter]"]').css('display', 'block');
    } else {
        jQuery('[name = "export_' + selected + '[' + selected + '_filter]"]').css('display', 'none');
    }
}

function submitForm() {
    var data = jQuery("#exportForm").serializeArray();
    jQuery.ajax({
        type: "POST",
        url: "./modules/centreon-awie/core/generateExport.php",
        data: data,
        success: function (data) {
            var errorMsg = '';
            oData = JSON.parse(data);
            jQuery('#pathFile').val(oData.fileGenerate);
            delete oData.fileGenerate;
            errorMsg += oData.error;
            errorMsg = errorMsg.replace(",", "\n");
            if (errorMsg.length !== 0 && errorMsg !== 'undefined') {
                alert(errorMsg);
            }
            jQuery("#downloadForm").submit();
        },
    });
    event.preventDefault();
}
