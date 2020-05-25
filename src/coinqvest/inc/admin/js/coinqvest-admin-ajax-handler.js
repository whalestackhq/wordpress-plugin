jQuery( document ).ready( function( $ ) {

    "use strict";

    $("#coinqvest_form_feedback").hide();

    $( '#coinqvest_ajax_form' ).submit( function( event ) {

        event.preventDefault();

        $.ajax({
            url: params.ajaxurl,
            dataType: 'json',
            type: 'post',
            data: $("#coinqvest_ajax_form").serialize() + '&ajaxrequest=true&submit=Submit+Form',
            success: function (response) {

                $("#coinqvest_form_feedback").removeClass().addClass(response.success === true ? "notice notice-success" : "notice notice-error").html(response.message).fadeIn();

                if (response.success === true && response.clear === true) {
                    $('form#coinqvest_ajax_form')[0].reset();
                }

                if(response.redirect) {
                    window.location = response.redirect;
                }

            }

        });

    });

});