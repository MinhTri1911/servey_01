$(document).ready(function() {
    var error = $('.data').attr('data-error');
    var notice = $('.data').attr('data-confirm');
    var link = $('.data').attr('data-link');
    var email_confirm = $('.data').attr('data-email-invalid');

    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.8&appId=1853033838292892";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    $(document).on('mousedown', '.glyphicon.glyphicon-sort', function(){
        $('.container-add-question').addClass('sortable');
        $('.sortable').sortable();
        $('.sortable').disableSelection();
    });

    $('.alert-message').delay(5000).slideUp(300);

    $('.save-survey').on('click', function () {
        var surveyId = $(this).attr('survey-id');
        var url = $(this).attr('data-url');
        var feature = $(this).attr('feature');
        var userId = $(this).attr('user-id');
        var answer = $('#wrapped').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;

            return obj;
        });

        $.post(
            url,
            {
                'surveyId': surveyId,
                'answer': answer,
                'feature': feature,
                'userId': userId,
            },
            function (response){
                if (response.success) {
                    $(".save-message-success").css("display", "block");
                    $('.save-message-success').html(response.message);
                } else {
                    $(".save-message-fail").css("display", "block");
                    $('.save-message-fail').html(response.message);
                }
        });
    });

    $('.show-survey').on('click', function () {
        var url = $(this).attr('data-url');
        var surveyId = $(this).attr('survey-id');

        $.get(
            url,
            {
                'surveyId': surveyId,
            },
            function (response) {
                if (response.success) {
                    $(".save-message-success").css("display", "block");
                    $('.save-message-success').html(response.message);
                    $('#container-survey').html(response.view);
                } else {
                    $(".save-message-fail").css("display", "block");
                    $('.save-message-fail').html(response.message);
                }
        });
    });

    $(document).on('click', '.qtyplus', function() {
        var getNumber = parseInt($('.quantity-limit').val());

        if (!isNaN(getNumber)) {
            $('.quantity-limit').val(getNumber + 1);
        } else {
            $('.quantity-limit').val(1);
        }
    });

    $(document).on('click', '.export-excel', function() {
        $('.exportExcel').click();
    });

    $(document).on('click', '.export-PDF', function() {
        $('.exportPDF').click();
    });

    $(document).on('click', '.qtyminus', function() {
        var getNumber = parseInt($('.quantity-limit').val());

        if (!isNaN(getNumber) && getNumber > 1) {
            $('.quantity-limit').val(getNumber - 1);
        } else {
            $('.quantity-limit').val(1);
        }
    });

    $(document).on('click', '#requirement-answer', function() {
        $('.setting-requirement').toggle(1200);
        $('.input-radio').prop('checked', false);
        $('#require-tail-email').prop('checked', false);
        $('#require-oneTime').prop('checked', false);
        $('.div-option-require').slideUp();
        $('.tail-email').slideUp();
        $('.frm-tailmail').tagsinput('removeAll');
    });

    $(document).on('click', '#limit-answer', function() {
        $('.setting-limit').toggle(1000);
        $('.quantity-limit').val('');
    });

    $(document).on('click', '.tab-choose', function() {
        var value = $(this).val();
        $('.content-' + value).css('display', 'block');

        for (var i = 1; i <= 4; i++) {
            if (i != value) {
                $('.content-' + i).css('display', 'none');
            }
        }
    });

    $('#countries').on('change', function () {
        var url = $(this).attr('data-url');
        var lang = $(this).val();

        $.get(
            url,
            {
                'locale': lang,
            },
            function (response) {
                if (response.success) {
                    window.location.href = response.urlBack;
                } else {
                    alert(error);
                }
        });
    });

    $('.submit-answer').prop('disabled', false);

    $('input[type=submit]').prop('disabled', false);

    $(document).on('click', '.active-answer', function() {
        $('.container-list-answer').fadeIn();
        $('.container-list-result').fadeOut();
    });

    $(document).on('click', '.active-result', function() {
        $('.container-list-result').fadeIn();
        $('.container-list-answer').fadeOut();
    });

    $('.image-type-option').hover(
        function() {
            $(this).siblings('span').css('display', 'block');
        }, function() {
            $(this).siblings('span').css('display', 'none');
        }
    );

    $("#countries").msDropDown();

    setTimeout(function() {
        $('.complete-image').addClass('jello');
    }, 1000);

    $(document).on('click', '.choose-image', function() {
        $('.button-file-hidden').click();
    });

    $(document).on('click', '.tag-send-email', function() {
        var url = $(this).attr('data-url');
        $('.frm-submit-mail').attr('action', url);
        $('.popupBackground').fadeIn();
    });

    $(document).on('click', '.btn-remove-survey', function() {
        var result = confirm(notice);

        if (result) {
            var url = $(this).attr('data-url');
            var idSurvey = $(this).attr('id-survey');
            var redirect = $(this).attr('redirect');
            $.post(
                url,
                {
                    'idSurvey': idSurvey,
                },
                function(response) {

                    if (response.success) {
                        window.location.href = redirect;
                    } else {
                        alert(error);
                }
            });
        }
    });

    $(document).on('click', '#require-tail-email', function() {
        if ($(this).prop('checked')) {
            $('.tail-email').slideDown();
        } else {
            $('.tail-email').slideUp();
            $('.frm-tailmail').tagsinput('removeAll');
        }
    });

    $(document).on('change', '.option-choose-answer', function() {
        if ($(this).val() == 1 || $(this).val() == 3) {
            $('.div-option-require').slideDown();
        } else {
            $('.div-option-require').slideUp();
            $('.tail-email').slideUp();
            $('.frm-tailmail').val('');
            $('#require-tail-email').prop('checked', false);
            $('#require-oneTime').prop('checked', false);
            $('.frm-tailmail').tagsinput('removeAll');
        }
    });

    $('.datetimepicker').datetimepicker();

    $('.frm-date-2').datetimepicker({
        format: 'MM/DD/YYYY'
    });

    $('.frm-time').datetimepicker({
        format: 'LT'
    });

    $(document).on('click', '.view-result', function() {
        window.location.href = $(this).attr('data-url');
    });

    $(document).on('click', '.menu-wizard', function() {
        $('.menu-wizard').removeClass('active-menu');
        $(this).addClass('active-menu');
    });

    $('.alert-message').delay(3000).slideUp(300);

    var number = parseInt($('.ct-data').attr('data-number'));
    var charts = $('.ct-data').attr('data-content');

    if (charts) {
        var obj = jQuery.parseJSON( charts );

        for (i = 0; i < obj.length; i++) {
            var dataInput = new Array();

            for (j = 0; j < obj[i]['chart'].length; j++) {
                dataInput.push([obj[i]['chart'][j]['answer'], obj[i]['chart'][j]['percent']]);
            }
            var count = i + 1;
            if(dataInput.length != 1) {
                Highcharts.chart('container' + i, {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 45,
                            beta: 0
                        },
                        style: {
                            fontFamily: 'Arial'
                        },
                        spacingBottom: 15,
                        spacingTop: 70,
                        spacingLeft: 70,
                        spacingRight: 70,
                    },
                    title: {
                        text: count + '.' + obj[i]['question']['content'],
                        floating: true,
                        align: 'left',

                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            depth: 35,
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}'
                            }
                        }
                    },
                    series: [{
                        type: 'pie',
                        name: 'Browser share',
                        data: dataInput
                    }]
                });
            }
        }
    }
});
