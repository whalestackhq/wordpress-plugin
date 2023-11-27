jQuery( document ).ready( function( $ ) {

    "use strict";

    $("form[id*='whalestack-checkout-form']").submit( function( event ) {

        event.preventDefault();

        var checkout_id = $(this).find('input[name=whalestack_checkout_id]').val();

        $("#whalestack-checkout-form-"+checkout_id+" input").removeClass("whalestack-error");
        $("#whalestack-checkout-form-"+checkout_id+" select").removeClass("whalestack-error");
        $("#whalestack-checkout-form-"+checkout_id+" .whalestack-show-button").hide();
        $("#whalestack-checkout-form-"+checkout_id+" .whalestack-show-loader").show();
        $("#whalestack-"+checkout_id+" .whalestack-close-modal").hide();

        var ajax_form_data = $(this).serialize();

        ajax_form_data = ajax_form_data+'&ajaxrequest=true&submit=Submit+Form';

        $.ajax({
            url: params.ajaxurl,
            dataType: 'json',
            type: 'post',
            data: ajax_form_data,
            success: function (response) {

                $("#whalestack-checkout-form-"+checkout_id+" .whalestack-show-button").show();
                $("#whalestack-checkout-form-"+checkout_id+" .whalestack-show-loader").hide();
                $("#whalestack-checkout-form-"+checkout_id+" .whalestack-feedback-row").show();
                $("#whalestack-"+checkout_id+" .whalestack-close-modal").show();

                $("#whalestack-checkout-form-"+checkout_id+" #whalestack-feedback").removeClass().addClass(response.success === true ? "whalestack-notice whalestack-success" : "whalestack-notice whalestack-error").html(response.message).fadeIn();

                if (response.highlightFields) {
                    response.highlightFields.forEach(function (item, index) {
                        $("form#whalestack-checkout-form-"+checkout_id+" input[name="+item+"]").addClass("whalestack-error");
                        $("form#whalestack-checkout-form-"+checkout_id+" select[name="+item+"]").addClass("whalestack-error");
                    });
                }

                if(response.redirect) {
                    window.location = response.redirect;
                }

                if (response.success === true && response.clear === true) {
                    $("form#whalestack-checkout-form-"+checkout_id)[0].reset();
                }

            }

        });

    });

});