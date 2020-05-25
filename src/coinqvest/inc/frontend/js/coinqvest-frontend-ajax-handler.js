jQuery( document ).ready( function( $ ) {

    "use strict";

    $("form[id*='coinqvest-checkout-form']").submit( function( event ) {

        event.preventDefault();

        var checkout_id = $(this).find('input[name=cq_checkout_id]').val();

        $("#coinqvest-checkout-form-"+checkout_id+" input").removeClass("cq-error");
        $("#coinqvest-checkout-form-"+checkout_id+" select").removeClass("cq-error");
        $("#coinqvest-checkout-form-"+checkout_id+" .cq-show-button").hide();
        $("#coinqvest-checkout-form-"+checkout_id+" .cq-show-loader").show();
        $("#coinqvest-"+checkout_id+" .cq-close-modal").hide();

        var ajax_form_data = $(this).serialize();

        ajax_form_data = ajax_form_data+'&ajaxrequest=true&submit=Submit+Form';

        $.ajax({
            url: params.ajaxurl,
            dataType: 'json',
            type: 'post',
            data: ajax_form_data,
            success: function (response) {

                $("#coinqvest-checkout-form-"+checkout_id+" .cq-show-button").show();
                $("#coinqvest-checkout-form-"+checkout_id+" .cq-show-loader").hide();
                $("#coinqvest-checkout-form-"+checkout_id+" .cq-feedback-row").show();
                $("#coinqvest-"+checkout_id+" .cq-close-modal").show();

                $("#coinqvest-checkout-form-"+checkout_id+" #cq-feedback").removeClass().addClass(response.success === true ? "cq-notice cq-success" : "cq-notice cq-error").html(response.message).fadeIn();

                if (response.highlightFields) {
                    response.highlightFields.forEach(function (item, index) {
                        $("form#coinqvest-checkout-form-"+checkout_id+" input[name="+item+"]").addClass("cq-error");
                        $("form#coinqvest-checkout-form-"+checkout_id+" select[name="+item+"]").addClass("cq-error");
                    });
                }

                if(response.redirect) {
                    window.location = response.redirect;
                }

                if (response.success === true && response.clear === true) {
                    $("form#coinqvest-checkout-form-"+checkout_id)[0].reset();
                }

            }

        });

    });

});