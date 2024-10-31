jQuery(document).ready(function () {
    jQuery(document).on('click', '#submit_user_query', function () {
        var btn = jQuery(this);
        var prompt_input = jQuery('#user_input');
        var reset_btn = jQuery('#reset_txtarea');
        var loader = jQuery('#show_loader');
        var textarea = jQuery('#user_input_output');
        if (jQuery('#user_input').val()) {
            var data = {
                prompt: jQuery('#user_input').val(),
                language: jQuery('#language').val(),
                writingStyle: jQuery('#writingStyle').val(),
                writingTone: jQuery('#writingTone').val(),
                nonce: vcwai_front.nonce,
            };
            jQuery.ajax({
                url: vcwai_front.rest_url + vcwai_front.get_prompts,
                data: data,
                dataType: 'json',
                type: 'POST',
                beforeSend: function (x) {
                    if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/json;charset=UTF-8");
                    }
                    loader.show();
                    btn.attr('disabled', true);
                    reset_btn.attr('disabled', true);
                },
                success: function (response) {
                    if (response.data) {
                        var txt = response.data;
                        console.log('txt', txt);
                        textarea.val(txt);
                    }
                    loader.hide();
                    btn.attr('disabled', false);
                    reset_btn.attr('disabled', false);

                }
            })
                .done(function () {
                    loader.hide();
                    btn.attr('disabled', false);
                    reset_btn.attr('disabled', false);
                })
                .fail(function () {
                    loader.hide();
                    btn.attr('disabled', false);
                    reset_btn.attr('disabled', false);
                })
                .always(function () {
                    loader.hide();
                    btn.attr('disabled', false);
                    reset_btn.attr('disabled', false);
                });
        }

    });
    jQuery(document).on('click', '#reset_txtarea', function () {
        jQuery('#user_input').val('');
        jQuery('#user_input_output').val('');

    });

});