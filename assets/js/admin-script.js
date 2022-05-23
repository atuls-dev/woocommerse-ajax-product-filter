jQuery( document ).ready(function($) {
    console.log(cfc_ajax.ajaxurl);
    var ajaxUrl = cfc_ajax.ajaxurl;

   /*  var options = {
            target:        '#output1',   // target element(s) to be updated with server response
            beforeSubmit:  showRequest,  // pre-submit callback
            success:       showResponse  // post-submit callback

            // other available options:
            url:       ajaxUrl         // override for form's 'action' attribute
            //type:      type        // 'get' or 'post', override for form's 'method' attribute
            //dataType:  null        // 'xml', 'script', or 'json' (expected server response type)
            //clearForm: true        // clear all form fields after successful submit
            //resetForm: true        // reset the form after successful submit

            // $.ajax options can be used here too, for example:
            //timeout:   3000
    };*/

   // $('#importForm').ajaxForm(options);

   
    $('body').on('submit', '#importForm', function(e) {
        e.preventDefault();

        var form_data = new FormData(this);
        $('.loader-icon').show();
        $.ajax({
            url: cfc_ajax.ajaxurl,
            type: 'POST',
            contentType: false,
            processData: false,
            data: form_data,
            success: function (response) {

                if ( response == "true" ) {
                    $('.admin-notices').text('File imported successfully...');
                    $("#importForm").trigger('reset');
                    $('.loader-icon').hide();
                }

                //alert('File uploaded successfully.');
            }
        });
    });    




});
