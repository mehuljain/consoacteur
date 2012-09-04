$(document).ready(function () {
    function loading_show() {
        $('#loading').html("<img src='images/loading.gif'/>").fadeIn('fast');
    }

    function loading_hide() {
        $('#loading').fadeOut();
    }

    function loadData(page) {
        loading_show();
        $.ajax
            ({
                type:"POST",
                url:"load_data.php",
                data:"page=" + page,
                success:function (msg) {
                    $("#container").ajaxComplete(function (event, request, settings) {
                        loading_hide();
                        $("#container").html(msg);
                    });
                }
            });
    }

    loadData(1); // For first time page load default results
    $('#container .pagination li.active').live('click', function () {
        var page = $(this).attr('p');
        loadData(page);
    });
});
