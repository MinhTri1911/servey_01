$(document).ready(function() {

    $('#langs').on('change', function () {
        var url = $(this).attr('data-url');
        var urlHome = $(this).attr('data-url-home');
        var lang = $(this).val();

        $.post(
            url,
            {
                'language': lang,
            },
            function (response) {
                if (response.success) {
                    window.location.href = response.urlBack;
                } else {
                    window.location.href = urlHome;
                }
        });
    });
});


