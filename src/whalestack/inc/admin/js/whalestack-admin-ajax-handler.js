jQuery( document ).ready( function( $ ) {

    "use strict";

    $("#whalestack_form_feedback").hide();

    $( '#whalestack_ajax_form' ).submit( function( event ) {

        event.preventDefault();

        $.ajax({
            url: params.ajaxurl,
            dataType: 'json',
            type: 'post',
            data: $("#whalestack_ajax_form").serialize() + '&ajaxrequest=true&submit=Submit+Form',
            success: function (response) {

                $("#whalestack_form_feedback").removeClass().addClass(response.success === true ? "notice notice-success" : "notice notice-error").html(response.message).fadeIn();

                if (response.success === true && response.clear === true) {
                    $('form#whalestack_ajax_form')[0].reset();
                }

                if(response.redirect) {
                    window.location = response.redirect;
                }

            }

        });

    });

});