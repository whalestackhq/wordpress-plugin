jQuery( document ).ready( function( $ ) {

    "use strict";

    $("#coinqvest_form_feedback").hide();

    $( '#coinqvest_ajax_form' ).submit( function( event ) {

        event.preventDefault();

        var ajax_form_data = $("#coinqvest_ajax_form").serialize();

        ajax_form_data = ajax_form_data+'&ajaxrequest=true&submit=Submit+Form';

        $.ajax({
            url: params.ajaxurl,
            dataType: 'json',
            type: 'post',
            data: ajax_form_data,
            success: function (response) {

                $("#coinqvest_form_feedback").removeClass().addClass(response.success === true ? "notice notice-success" : "notice notice-error").html(response.message).fadeIn();

                if(response.redirect) {
                    window.location = response.redirect;
                }

                if (response.success === true && response.clear === true) {
                    $('form#coinqvest_ajax_form')[0].reset();
                }

            }

        });

    });

});