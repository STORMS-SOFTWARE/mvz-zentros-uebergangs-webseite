//*********************************************
//  CONTACT FORM SCRIPTS
//*********************************************

$(function () {

    //Input effects
    $(".contact .contact-input").each(function () {
        //Focus In
        $(this).focusin(function () {
            $(this).parent().addClass("focused");
        });
        //Focus out
        $(this).focusout(function () {
            if ($(this).val().length === 0) {
                $(this).parent().removeClass('focused');
            }
        });
    });

    //If contact form is not visible
    /*$('.contact-form').each(function () {
        var elem = $(this);
        $(elem).waypoint(function (direction) {
            if (direction == 'up') {
                $(elem).addClass('unvisible');
            } else {
                $(elem).removeClass('unvisible');
            }
            $(elem).toggleClass('unvisible');
        }, {offset: '0%'});
    });*/

    //Contact Form Settings
    var validator = $('.contact-form, .newsletter-form');
    var rnuma = Math.floor(Math.random() * 5);
    var rnumb = Math.floor(Math.random() * 5);
    var sum = rnuma + rnumb;
    $('<textarea id="math" style="display:none;">' + sum + '</textarea>').insertAfter(validator);
    $("#verify-label span").html(rnuma + "+" + rnumb + "= ?");

    // Validate Contact Form
    $(validator).each(function () {
        var sendBtn = $(this).find(':submit'),
            $this = $(this),
            timer = window.setTimeout(3500);

        $(sendBtn).on("click", function () {
            if ($($this).hasClass("unvisible")) {
                $('html, body').stop().animate({scrollTop: $($this).offset().top - 70}, 1000, 'easeInOutExpo');
            }
        });
        // Classic Zeplin Validate
        $(this).validate({
            ignore: ".ignore",
            rules: {
                verify: {
                    required: true,
                    equalTo: "#math"
                },
                hiddenRecaptcha: {
                    required: function () {
                        if (grecaptcha.getResponse() === '') {
                            $($this).find('.g-recaptcha').addClass('error_warning');
                            return true;
                        } else {
                            $($this).find('.g-recaptcha').removeClass('error_warning');
                            return false;
                        }
                    }
                }
            },
            showErrors: function (map, list) {
                this.currentElements.removeClass("error_warning");
                $.each(list, function (index, error) {
                    window.clearTimeout(timer);
                    if ($($this).hasClass("contact-form")) {
                        $($this).parent().find(".error-messages").addClass("show error").removeClass("success");
                        $($this).addClass("error-message-showing");
                        window.clearTimeout(timer);
                    }
                    $(error.element).addClass("error_warning");
                });
            },
            submitHandler: function (form) {
                $(sendBtn).not('.loading').addClass('loading').append("<span class='loader'></span>");
                $($this).find('label').addClass("ok");
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: new FormData($(form)[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function () {
                        if ($($this).hasClass("contact-form")) {
                            $($this).addClass("error-message-showing");
                            $($this).parent().find(".error-messages").addClass("show success").removeClass("error");
                            timer = window.setTimeout(function () {
                                $($this).removeClass("error-message-showing");
                                $($this).parent().find(".error-messages").removeClass("success");
                            }, 5000);
                        }
                        $(sendBtn).removeClass('loading');
                        $(".focused").removeClass("focused");
                        $(validator).trigger("reset").addClass("reseting");
                        setTimeout(function () {
                            $(validator).removeClass("reseting");
                        }, 1000);
                        if ($this.hasClass("newsletter-form")) {
                            $("footer .footer-newsletter").addClass("success");
                        }
                    }
                });
            }
        });

    });

});
