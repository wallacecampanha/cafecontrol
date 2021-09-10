$(function () {
    $("form").submit(function (e) {
        e.preventDefault();

        var form = $(this);
        var load = $(".ajax_load");

        load.fadeIn(200).css("display", "flex");

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: form.serialize(),
            dataType: "json",
            success: function (response) {
                //redirect
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    load.fadeOut(200);
                }

                //Error
                if (response.message) {
                    $(".ajax_response").html(response.message).effect("bounce");
                }
            },
            error: function (response) {
                load.fadeOut(200);
            }
        });
    });
});