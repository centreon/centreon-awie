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


/**
 *
 * @param uploadField
 */
function checkSize(uploadField) {
    if (uploadField.files[0].size > 50000) {
        alert("Fichier trop volumineux, ne pas dépasser les 500ko");
        uploadField.value = "";
    } else if (uploadField.selectedIndex === 0) {
        alert("Fichier vide");
        uploadField.value = "";
    }

}

/**
 *
 */
function submitForm() {
    jQuery(".loadingWrapper").css('display', 'block');
    var formData = new FormData();
    var importFiles = jQuery('#file')[0].files;
    formData.append('clapiImport', importFiles[0])
    formData.append('action', 'ajax_file_import');
    jQuery.ajax({
        type: "POST",
        url: "./modules/centreon-awie/core/launchImport.php",
        data: formData,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (data) {
            var errorMsg = '';
            errorMsg += data.error;
            if (errorMsg.length !== 0 && errorMsg !== 'undefined') {
                errorMsg = errorMsg.replace(",", "\n");
                alert(errorMsg);
            } else {
                var log = data.success;
                var tabLog = log.split('\n');
                for (var i = 0; i < tabLog.length; i++) {
                    jQuery('#logLine').append('<p>' + tabLog[i] +'</p>');
                }
                jQuery('#logClapi').css('display', 'block');
            }
            jQuery(".loadingWrapper").css('display', 'none');
        },
    });
    event.preventDefault();
}


function viewLog() {
    var elem = jQuery('#logLine').css('display');
    if (elem === 'none') {
        jQuery('#logLine').css('display', 'block');
    } else {
        jQuery('#logLine').css('display', 'none');
    }
}
