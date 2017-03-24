$(document).ready(function() {
    var url = $('.url-token').data('route');

    $(document).on("click", ".remove-sva", function() {
        var idSurvey = $(this).attr("id-survey");
        $.post(
            url + '/admin/destroy-survey',
            {
                'idSurvey': idSurvey,
            },
            function(response) {
                $(".sva" + idSurvey).remove();
        });
    });

    $(document).on("click", ".remove-svb", function() {
        var idSurvey = $(this).attr("id-survey");
        $.post(
            url + '/admin/destroy-survey',
            {
                'idSurvey': idSurvey,
            },
            function(response) {
                $(".svb" + idSurvey).remove();
        });
    });

    $('.alert-message').delay(3000).slideUp(300);
});



//# sourceMappingURL=survey.js.map
