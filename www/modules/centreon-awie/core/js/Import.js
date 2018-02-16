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
    var formData = new FormData();
    var importFiles = jQuery('#file')[0].files;
    formData.append('clapiImport', importFiles[0])
    formData.append('action', 'ajax_file_import');

    jQuery.ajax({
        type: "POST",
        url: "./modules/centreon-awie/core/launchImport.php",
        data: formData,
        cache: false,
        dataType: 'json', // This replaces dataFilter: function() && JSON.parse( data ).
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string reques.
        success: function (data) {
        },


        //     var errorMsg = '';
        //     oData = JSON.parse(data);
        //     jQuery('#pathFile').val(oData.fileGenerate);
        //     delete oData.fileGenerate;
        //     errorMsg += oData.error;
        //     errorMsg = errorMsg.replace(",", "\n");
        //     if (errorMsg.length !== 0 && errorMsg !== 'undefined') {
        //         alert(errorMsg);
        //     }
        //     jQuery("#downloadForm").submit();
        //     jQuery(".loadingWrapper").css('display', 'none');
        // },


    });
    event.preventDefault();
}


//
// jQuery('#importForm').submit( function( event ) {
//
//     alert('toto');
//     event.stopPropagation(); // Stop stuff happening
//     event.preventDefault(); // Totally stop stuff happening
//
//     var formData = new FormData();
//     var importFiles = jQuery('#importForm')[0].files;
//
//
//
//     // For EACH file, append to formData.
//     // NOTE: Just appending all of importFiles doesn't transition well to PHP.
//     jQuery.each( importFiles, function( index, value ) {
//         var name = 'file_' + index;
//         formData.append( name, value )
//     });
//
//     formData.append( 'action', 'ajax_file_import' );
//     formData.append( '_ajax_nonce', importNonce );
//
//     jQuery.ajax({
//         url: ajaxurl,
//         type: 'POST',
//         data: formData,
//         cache: false,
//         dataType: 'json', // This replaces dataFilter: function() && JSON.parse( data ).
//         processData: false, // Don't process the files
//         contentType: false, // Set content type to false as jQuery will tell the server its a query string request
//
//
//         beforeSend: function( jqXHR, settings ){
//             // (OPTIONAL) Alt. AJAX concept.
//             // Removes the old IFrame if any.
//             var element = document.getElementById('import_IF');
//             if ( element !== null ){
//                 element.parentNode.removeChild( element );
//             }
//             // END (OPTIONAL).
//         },
//         success: function( data, textStatus, jqXHR ) {
//             console.log( 'Return from PHP AJAX function/method.' );
//
//             // Do stuff with data.values
//
//             // (OPTIONAL) Alt. AJAX concept.
//             // Required for downloading in AJAX.
//             var paramStr = '';
//             paramStr += '?_ajax_nonce=' + data._ajax_nonce;
//             paramStr += '&action='      + data.action;
//             paramStr += '&filename='    + data.filename;
//
//             var elemIF = document.createElement("iframe");
//             elemIF.id = 'import_IF'
//             elemIF.style.display = "none";
//             elemIF.src = ajaxurl + paramStr;
//
//             document.body.appendChild(elemIF);
//             elemIF.parentNode.removeChild(elemIF);
//             // END (OPTIONAL).
//         },
//         complete: function(jqXHR, textStatus){
//             console.log( 'AJAX is complete.' );
//
//             // (OPTIONAL) Alt. AJAX concept.
//             // Clean up IFrame.
//             var element = document.getElementById('import_IF');
//             if ( element !== null ){
//                 element.parentNode.removeChild( element );
//             }
//             // END (OPTIONAL).
//         }
//     });// End AJAX.
// });// End .submit().
//









