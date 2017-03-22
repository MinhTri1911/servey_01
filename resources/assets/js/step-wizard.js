$(document).ready(function() {
    var error = $('.data').attr('data-error');
    var notice = $('.data').attr('data-confirm');
    var email_confirm = $('.data').attr('data-email-invalid');

    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        return re.test(email);
    }

    function validateTailmail(email) {
        var text = /^@[A-Z0-9-]+(\.+([A-Z]+)){1,4}$/i;

        return text.test(email);
    }

    function formatDate(date) {
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        hours   = hours < 10 ? '0' + hours : hours ;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        var strTime = hours + " : " + minutes + ' ' + ampm;

        return date.getFullYear() + "/" + ((date.getMonth() + 1) < 10 ? "0" +
            (date.getMonth() + 1) : (date.getMonth() + 1) ) + "/" +
            (date.getDate() < 10 ? "0" + date.getDate() : date.getDate()) + " " + strTime;
    }

    $("#survey_container").wizard({
        stepsWrapper: "#middle-wizard",
        beforeForward: function( event, state ) {
            if ( state.stepIndex === 1 ) {
                var today = new Date();
                var currentDateTime = formatDate(today);
                var dealineTime = $('.frm-deadline').val();
                var timeChoosed   = formatDate (new Date(Date.parse(dealineTime)));

                if ( dealineTime.length != 0 && currentDateTime > timeChoosed){
                    $('.validate-time').css('display', 'block');

                    return false;
                }

            } else if ( state.stepIndex === 2 ) {
                $('html, body').animate({scrollTop: 0}, 500);

                if($('.data').attr('data-question') == 0) {
                    $('.create-question-validate')
                        .css('display', 'block')
                        .removeClass('fadeOutUp')
                        .addClass('animated fadeInDown');

                    return false;
                }
            } else if ( state.stepIndex === 3 ) {
                    $('html, body').animate({scrollTop: 0}, 1000);
                    var flag = temp = true;
                    var c1 = $('#requirement-answer').is(':checked');
                    var c2 = $('.option-choose-answer').is(':checked');
                    var c3 = $('#limit-answer').is(':checked');
                    var c4 = $('.quantity-limit').val();
                    var c5 = c4 % 1;
                    var c6 = $('#require-tail-email').is(':checked');
                    var c7 = $('.frm-tailmail').val();
                    var tailMails = $('.frm-tailmail').tagsinput('items');

                    tailMails.forEach(function (tailemail) {

                        if (!validateTailmail(tailemail)) {
                            temp = false;
                        }
                    });

                    if ((c1 && !c2) ){
                        $('.validate-requirement-answer')
                            .css('display', 'block')
                            .addClass('animated fadeInDown')
                            .delay(3000)
                            .slideUp(1000);
                        flag = false;
                    }

                    if ((c3 && !c4.length) || (c3 && c4.length && (c5 || parseInt(c4) < 1))) {
                        $('.validate-limit-answer')
                            .css('display', 'block')
                            .addClass('animated fadeInDown')
                            .delay(3000)
                            .slideUp(1000);
                        flag = false;
                    }

                    if ((c6 && !c7.length) || (c6 && c7.length && !temp)) {
                        $('.validate-tailmail')
                            .css('display', 'block')
                            .addClass('animated fadeInDown')
                            .delay(3000)
                            .slideUp(1000);
                        flag = false;
                    }

                    return flag;

                } else if ( state.stepIndex === 4 ) {
                    var emails = $('input:text[name=emails]').tagsinput('items');
                    var flag = true;
                    emails.forEach(function (email) {

                        if (!validateEmail(email)) {
                            flag = false;
                        }
                    });

                    if (emails.length != 0 && !flag) {
                        $('.validate-email').css('display', 'block')

                        return false;
                    }

                    $('.validate-email').css('display', 'none');
            }
        }
    });

    $(document).on('click', '.btn-save-setting', function() {
        var flag = temp = true;
        var c1 = $('#requirement-answer').is(':checked');
        var c2 = $('.option-choose-answer').is(':checked');
        var c3 = $('#limit-answer').is(':checked');
        var c4 = $('.quantity-limit').val();
        var c5 = c4 % 1;
        var c6 = $('#require-tail-email').is(':checked');
        var c7 = $('.frm-tailmail').val();
        var tailMails = $('.frm-tailmail').tagsinput('items');

        tailMails.forEach(function (tailemail) {

            if (!validateTailmail(tailemail)) {
                temp = false;
            }
        });

        if ((c1 && !c2) ){
            $('.validate-requirement-answer')
                .css('display', 'block')
                .addClass('animated fadeInDown')
                .delay(3000)
                .slideUp(1000);
            flag = false;
        }

        if ((c3 && !c4.length) || (c3 && c4.length && (c5 || parseInt(c4) < 1))) {
            $('.validate-limit-answer')
                .css('display', 'block')
                .addClass('animated fadeInDown')
                .delay(3000)
                .slideUp(1000);
            flag = false;
        }

        if ((c6 && !c7.length) || (c6 && c7.length && !temp)) {
            $('.validate-tailmail')
                .css('display', 'block')
                .addClass('animated fadeInDown')
                .delay(3000)
                .slideUp(1000);
            flag = false;
        }

        return flag;
    });

    $(document).on('click', '.btn-close-popup', function() {
        $('.popupBackground').fadeOut(500);
        setTimeout(function() {
            $('.popupBackground').css('display', 'none');
        }, 500);
    });

    $(document).on('click', '.image-type-option', function() {
        $('.create-question-validate')
            .removeClass('fadeInDown')
            .addClass('fadeOutUp');
        $('.create-question-validate').slideUp();
        setTimeout(function() {
            $('.create-question-validate').slideUp().css('display', 'none');
        }, 1000);
    });

    $(document).on('click', '.btn-send-mail', function() {
        var emails = $('.frm-mail-user').tagsinput('items');
        var input_email = $('.input-email').val();
        var flag = true;

        emails.forEach(function (email) {

            if (!validateEmail(email)) {
                flag = false;
            }
        });

        if ((emails.length != 0 && !flag)
            || !emails.length
            || !input_email.length
            || !validateEmail(input_email)
            ) {
            $('.validate-send-email').css('display', 'block');

            setTimeout(function() {
                $('.validate-send-email').slideUp(1000);
            }, 4000);

            return false;
        }

        return true;
    });

    $(document).on('click', '.submit-survey', function() {
        if(!$('.quantity-limit').val().length) {
            $('.setting-limit').remove();
        }
    });
});
