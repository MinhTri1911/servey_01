$(document).ready(function() {
    var error = $('.data').attr('data-error');
    var sumImageSize = 0;
    var obj = [];
    var arrayQuestion = [];
    var arrayAnswer = [];
    var arrayImageAnswer = [];
    var arrayImageQuestion = [];
    var size = 0;
    var getProperty = function (propertyName) {
        return obj[propertyName];
    };

    function addAnwser($this) {
        var url = $this.attr('url');
        var type = $this.attr('typeId');
        var number = parseInt($($this).attr('id-as'));
        var trash = parseInt($('.question' + number).attr('trash'));
        var num_as = (parseInt($('.question' + number).attr('temp-qs')) + 1);
        $.post(
            url,
            {
                'number': number,
                'num_as': num_as,
                'type': type,
            },
            function(response) {

                if (response.success) {
                    $('.temp-other' + number +':first').before(response.data);
                    $('.question' + number).attr('temp-qs', num_as);
                    $('.question' + number).attr('trash', trash + 1);
                } else {
                    alert(error);
                }
        });
    }

    function addOtherButton($this, class_temp) {
        var number = parseInt($this.parent().parent().find(class_temp).attr('id-as'));
        var type = $this.attr('typeId');
        var url = $this.attr('url');
        $.post(
            url,
            {
                'number': number,
                'type': type,
            },
            function(response) {

                if (response.success) {
                    var trash = parseInt($('.question' + number).attr('trash'));
                    $('.question' + number).attr('trash', trash + 1);
                    $('.temp-other' + number + ':first').before(response.data);
                } else {
                    alert(error);
                }
        });
        $this.hide();
    }

    function addQuestion($this) {
        var number = parseInt($('.data').attr('data-number')) + 1;
        var number_qs = parseInt($('.data').attr('data-question')) + 1;
        var type = $this.attr('typeId');
        var url = $this.attr('url');
        $.post(
            url,
            {
                'number': number,
                'type': type,
            },
            function(response) {

                if (response.success) {
                    $('.hide').before(response.data);
                    $('.data').attr('data-number', number);
                    $('.data').attr('data-question', number_qs);
                    $('.div-finish').css('display', 'block');
                } else {
                    alert(error);
                }
        });
    }

    function checkTypeImage(input) {
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
        var fileName = input.name;
        var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);

        if ($.inArray(fileNameExt. toLowerCase(), fileExtension) == -1){

           return false;
        }

        return true;
    }

    function readURL(input, $class) {
        // sumImageSize += input.files[0].size/1024/1024;
        var key = $class;

        if (input.files && input.files[0] ) {
            obj[key] = input.files[0].size;
            size++;
            var reader = new FileReader();

            reader.onload = function (e) {
                $($class).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            // sumImageSize -= input.files[0].size/1024/1024;
            obj[key] = 0;

            return false;
        }
    }

    $(document).on('click', '.add-radio', function() {
        addAnwser($(this));
    });

    $(document).on('click', '.add-checkbox', function() {
        addAnwser($(this));
    });

    $(document).on('click', '.add-radio-other',  function() {
        var class_temp = '.add-radio';
        addOtherButton($(this), class_temp);
    });

    $(document).on('click', '.add-checkbox-other', function() {
        var class_temp = '.add-checkbox';
        addOtherButton($(this), class_temp);
    });

    $(document).on('click', '#radio-button', function() {
        addQuestion($(this));
    });

    $(document).on('click', '#checkbox-button', function() {
        addQuestion($(this));
    });

    $(document).on('click', '#text-button', function() {
        addQuestion($(this));
    });

    $(document).on('click', '#time-button', function() {
        addQuestion($(this));
    });

    $(document).on('click', '#date-button', function() {
        addQuestion($(this));
    });

    $(document).on('click', '.title-question', function() {
        $(this).children('.choose-action').fadeIn(1700);
        $('.title-question').css('box-shadow', '').css('border-radius', '');
        $(this).css('box-shadow', '0px 2px 10px rgba(119, 132, 130, 0.56)')
            .css('border-radius', '5px');
    });

    $(document).on('click', '.glyphicon-picture', function() {
        var idQuestion = $(this).siblings('.glyphicon-trash').attr('id-question');
        $('.fileImg' + idQuestion).click();
        $(document).on('change', '.fileImg' + idQuestion, function () {
            var image = readURL(this, '.image-question' + idQuestion);

            if (image == false) {
                $('.image-question' + idQuestion).attr('src', '/');
                $('.content-image-question' + idQuestion).css('display', 'none');
            } else {
                $('.content-image-question' + idQuestion).css('display', 'block');
            }
        });
    });

    $(document).on('click', '.remove-image-question', function() {
        var idQuestion = $(this).attr('id-question');
        var idImage = $(this).attr('data-id-image');
        arrayImageQuestion.push(idImage);
        $('.content-image-question' + idQuestion).fadeOut(500);
        setTimeout(function() {
            sumImageSize -= getProperty('.image-question' + idQuestion);
            obj['.image-question' + idQuestion] = 0;
            $('.image-question' + idQuestion).attr('src', '/');
            $('.content-image-question' + idQuestion).css('display', 'none');
        }, 1000);

    });

    $(document).on('click', '.btn-change-survey', function() {
        $('input[name=del-question]').val(arrayQuestion);
        $('input[name=del-answer]').val(arrayAnswer);
        $('input[name=del-question-image]').val(arrayImageQuestion);
        $('input[name=del-answer-image]').val(arrayImageAnswer);

        // return false;
    });

    $(document).on('click', '.show-multi-history', function() {
        var url = $(this).attr('data-url');
        $.get(
            url,
            function(response) {

                if (response.success) {
                    $('body').css('overflow', 'hidden');
                    $('.popup-user-answer').css('display', 'block');
                    $('.popup-content-history').append(response.data);
                } else {
                    alert(error);
                }
        });
    });

    $(document).on('click', '.hidden-result', function() {
        $('body').css('overflow', 'auto');
        $('.popup-user-answer').css('display', 'none');
        $('.popup-content-history').empty();
    });

    $(document).on('click', '.picture-answer', function() {
        var idAnswer = $(this).siblings('.glyphicon-remove').attr('id-as');
        $('.fileImgAnswer' + idAnswer).click();
        $(document).on('change', '.fileImgAnswer' + idAnswer, function () {
            var image = readURL(this, '.image-answer' + idAnswer);

            if (image == false) {
                $('.image-answer' + idAnswer).attr('src', '/');
                $('.content-image-answer' + idAnswer).css('display', 'none');
            } else {
                $('.content-image-answer' + idAnswer).css('display', 'block');
            }
        });
    });

    $(document).on('click', '.remove-image-answer', function() {
        var idAnswer = $(this).attr('id-answer');
        var idImage =  $(this).attr('data-answerid');
        $('.content-image-answer' + idAnswer).fadeOut(500);
        arrayImageAnswer.push(idImage);

        setTimeout(function() {
            sumImageSize -= getProperty('.image-answer' + idAnswer);
            obj['.image-answer' + idAnswer] = 0;
            $('.image-answer' + idAnswer).attr('src', '/');
            $('.content-image-answer' + idAnswer).css('display', 'none');
        }, 1000);

    });

    $(document).on('click', '.btn-require-answer', function() {
        addQuestion($(this));
    });

    $(document).on('click', '.glyphicon-trash', function() {
        var number_qs = parseInt($('.data').attr('data-question')) - 1;
        var idQuestion = $(this).attr('id-question');
        var sum = 0;
        arrayQuestion.push(idQuestion);

        if(number_qs == 0) {
            $('.div-finish').css('display', 'none');
        }

        for (var i = 0; i < size; i++) {
            if (getProperty('.image-answer' + idQuestion + i)) {
                sum += getProperty('.image-answer' + idQuestion + i);
                obj['.image-answer' + idQuestion + i] = 0;
            }
        }

        sumImageSize = sumImageSize - (getProperty('.image-question' + idQuestion) + sum);
        obj['.image-question' + idQuestion] = 0;
        $('.data').attr('data-question', number_qs);
        $('.question' + idQuestion).removeClass('animate zoomIn');
        $('.question' + idQuestion).addClass('animate fadeOutDown');
        setTimeout(function() {
          $('.question' + idQuestion).remove();
        }, 1000);
    });

    $(document).on('click', '.btn-remove-answer', function() {
        var number = parseInt($(this).attr('num'));
        var idAnswer = $(this).attr('id-as');
        var idDelete = $(this).attr('data-answerId');
        var trash = parseInt($('.question' + number).attr('trash'));

        if (trash > 2) {
            arrayAnswer.push(idDelete);
            sumImageSize -= getProperty('.image-answer' + idAnswer);

            obj['.image-answer' + idAnswer] = 0;
            $('.question' + number).attr('trash', trash - 1);
            $('.clear-as' + idAnswer).remove();
            $('.qs-as' + idAnswer).remove();
        }
    });

    $(document).on('click', '.aaaaaa', function() {
        alert(arrayImageQuestion);
    });

    $(document).on('click', '.remove-other', function() {
        var idQuestion = $(this).attr('id-qs');
        var trash = parseInt($('.question' + idQuestion).attr('trash'));

        if (trash > 2) {
            $('.question' + idQuestion).attr('trash', trash - 1);
            $('.temp-other' + idQuestion + ':last').remove();
            $('.answer-other' + idQuestion).remove();
            $('.other' + idQuestion).show();
        }
    });

    $(document).on('click', '.bt-action', function() {
        var url = $(this).attr('url');
        window.location = url;
    });

    $(document).on('click', '.option-add', function() {
        var url = $(this).attr('url');
        var temp_as = $(this).attr('temp-as');
        var temp_qs = $(this).attr('temp-qs');

        if ($(this).prop('checked')) {
            $.post(
                url,
                {
                    'idQuestion': temp_qs,
                    'idAnswer': temp_as,
                },
                function(response) {

                    if (response.success) {
                        $('.append-as' + temp_qs).html(response.data);
                    } else {
                        alert(error);
                    }
            });
        } else {
            $('.input' + temp_qs).remove();
        }
    });

    $(document).on('click', '.option-choose', function() {
        var temp_qs = $(this).attr('temp-qs');
        $('.input' + temp_qs).remove();
    });
});

//# sourceMappingURL=question.js.map
