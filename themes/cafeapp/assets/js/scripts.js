$(function () {
    var effecttime = 200;

    /*
     * MOBILE MENU
     */
    $("[data-mobilemenu]").click(function (e) {
        var clicked = $(this);
        var action = clicked.data("mobilemenu");

        if (action === 'open') {
            $(".app_sidebar").slideDown(effecttime);
        }

        if (action === 'close') {
            $(".app_sidebar").slideUp(effecttime);
        }
    });

    // scroll animate
    $("[data-go]").click(function (e) {
        e.preventDefault();

        var goto = $($(this).data("go")).offset().top;
        $("html, body").animate({scrollTop: goto}, goto / 2);
    });

    /*
     * APP MODAL
     */
    $("[data-modalopen]").click(function (e) {
        var clicked = $(this);
        var modal = clicked.data("modalopen");
        $(".app_modal").fadeIn(effecttime).css("display", "flex");
        $(modal).fadeIn(effecttime);
    });

    $("[data-modalclose]").click(function (e) {
        if (e.target === this) {
            $(this).fadeOut(effecttime);
            $(this).children().fadeOut(effecttime);
        }
    });

    /*
     * FROM CHECKBOX
     */
    $("[data-checkbox]").click(function (e) {
        var checkbox = $(this);
        checkbox.parent().find("label").removeClass("check");
        if (checkbox.find("input").is(':checked')) {
            checkbox.addClass("check");
        } else {
            checkbox.removeClass("check");
        }
    });

    /*
     * FADE
     */
    $("[data-fadeout]").click(function (e) {
        var clicked = $(this);
        var fadeout = clicked.data("fadeout");
        $(fadeout).fadeOut(effecttime, function (e) {
            if (clicked.data("fadein")) {
                $(clicked.data("fadein")).fadeIn(effecttime);
            }
        });
    });

    $("[data-fadein]").click(function (e) {
        var clicked = $(this);
        var fadein = clicked.data("fadein");
        $(fadein).fadeIn(effecttime, function (e) {
            if (clicked.data("fadeout")) {
                $(clicked.data("fadeout")).fadeOut(effecttime);
            }
        });
    });

    /*
     * SLIDE
     */
    $("[data-slidedown]").click(function (e) {
        var clicked = $(this);
        var slidedown = clicked.data("slidedown");
        $(slidedown).slideDown(effecttime);
    });

    $("[data-slideup]").click(function (e) {
        var clicked = $(this);
        var slideup = clicked.data("slideup");
        $(slideup).slideUp(effecttime);
    });

    /*
     * TOOGLE CLASS
     */
    $("[data-toggleclass]").click(function (e) {
        var clicked = $(this);
        var toggle = clicked.data("toggleclass");
        clicked.toggleClass(toggle);
    });

    /*
     * jQuery MASK
     */
    $(".mask-money").mask('000.000.000.000.000,00', {reverse: true, placeholder: "0,00"});
    $(".mask-date").mask('00/00/0000', {reverse: true});
    $(".mask-month").mask('00/0000', {reverse: true});
    $(".mask-doc").mask('000.000.000-00', {reverse: true});
    $(".mask-card").mask('0000  0000  0000  0000', {reverse: true});

    /*
     * AJAX FORM
     */
    $("form:not('.ajax_off')").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        var load = $(".ajax_load");
        var flashClass = "ajax_response";
        var flash = $("." + flashClass);

        form.ajaxSubmit({
            url: form.attr("action"),
            type: "POST",
            dataType: "json",
            beforeSend: function () {
                load.fadeIn(200).css("display", "flex");
            },
            uploadProgress: function (event, position, total, completed) {
                var loaded = completed;
                var load_title = $(".ajax_load_box_title");
                load_title.text("Enviando (" + loaded + "%)");

                form.find("input[type='file']").val(null);
                if (completed >= 100) {
                    load_title.text("Aguarde, carregando...");
                }
            },
            success: function (response) {
                //redirect
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    load.fadeOut(200);
                }

                //reload
                if (response.reload) {
                    window.location.reload();
                } else {
                    load.fadeOut(200);
                }

                //message
                if (response.message) {
                    if (flash.length) {
                        flash.html(response.message).fadeIn(100).effect("bounce", 300);
                    } else {
                        form.prepend("<div class='" + flashClass + "'>" + response.message + "</div>")
                            .find("." + flashClass).effect("bounce", 300);
                    }
                } else {
                    flash.fadeOut(100);
                }
            },
            complete: function () {
                if (form.data("reset") === true) {
                    form.trigger("reset");
                }
            },
            error: function () {
                var message = "<div class='message error icon-warning'>Desculpe mas não foi possível processar a requisição. Favor tente novamente!</div>";

                if (flash.length) {
                    flash.html(message).fadeIn(100).effect("bounce", 300);
                } else {
                    form.prepend("<div class='" + flashClass + "'>" + message + "</div>")
                        .find("." + flashClass).effect("bounce", 300);
                }

                load.fadeOut();
            }
        });
    });

    /*
     * APP ON PAID
     */
    $("[data-onpaid]").click(function (e) {
        var clicked = $(this);
        var dataset = clicked.data();

        $.post(clicked.data("onpaid"), dataset, function (response) {
            //reload by error
            if (response.reload) {
                window.location.reload();
            }

            //Balance
            $(".j_total_paid").text("R$ " + response.onpaid.paid);
            $(".j_total_unpaid").text("R$ " + response.onpaid.unpaid);
        }, "json");
    });

    /*
     * IMAGE RENDER
     */
    $("[data-image]").change(function (e) {
        var changed = $(this);
        var file = this;

        if (file.files && file.files[0]) {
            var render = new FileReader();

            render.onload = function (e) {
                $(changed.data("image")).fadeTo(100, 0.1, function () {
                    $(this).css("background-image", "url('" + e.target.result + "')")
                        .fadeTo(100, 1);
                });
            };
            render.readAsDataURL(file.files[0]);
        }
    });

    /*
     * APP INVOICE REMOVE
     */
    $("[data-invoiceremove]").click(function (e) {
        var remove = confirm("ATENÇÃO: Essa ação não pode ser desfeita! Tem certeza que deseja excluir esse lançamento?");

        if (remove === true) {
            $.post($(this).data("invoiceremove"), function (response) {
                //redirect
                if (response.redirect) {
                    window.location.href = response.redirect;
                }
            }, "json");
        }
    });

    /*
     * WALLET FILTER
     */
    $(".app_header_widget .wallet").mouseenter(function () {
        $(this).find("ul").slideDown(200);
    }).mouseleave(function () {
        $(this).find("ul").slideUp(200);
    });

    $("[data-walletfilter]").click(function (e) {
        var wallet = $(this).data("wallet");
        var endpoint = $(this).data("walletfilter");

        $(".ajax_load")
            .fadeIn(200)
            .css("display", "flex")
            .find(".ajax_load_box_title")
            .text("Aguarde, abrindo carteira...");

        $.post(endpoint, {wallet: wallet}, function (e) {
            window.location.reload();
        }, "json");
    });

    /*
     * WALLET EDIT
     */
    $("[data-walletedit]").change(function () {
        var wallet = $(this).val();
        var endpoint = $(this).data("walletedit");
        $.post(endpoint, {wallet_edit: wallet}, "json");
    });

    /*
     * WALLET DELETE
     */
    $(".wallet_action").click(function () {
        $(this).parent().find(".wallet_overlay").fadeIn(200).css("display", "flex");
    });

    $(".wallet_overlay_close").click(function () {
        $(this).parents(".wallet").find(".wallet_overlay").fadeOut(200);
    });

    $("[data-walletremove]").click(function () {
        var wallet = $(this).data("wallet");
        var endpoint = $(this).data("walletremove");

        $(".ajax_load").fadeIn(200).css("display", "flex").find(".ajax_load_box_title").text("Removendo carteira...");
        $.post(endpoint, {wallet_remove: wallet}, function (e) {
            window.location.reload();
        });
    });
});