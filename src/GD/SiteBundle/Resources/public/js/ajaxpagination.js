$(document).ready(function () {

    function loadData(page) {

        $.ajax
            ({
                type:"POST",
                url:"load_data.php",
                data:"page=" + page,
                success:function (msg) {
                    $("#container").ajaxComplete(function (event, request, settings) {
                        $("#container").html(msg);
                    });
                }
            });
    }

    loadData(1);  // For first time page load default results
    $('#container .pagination li.active').live('click', function () {
        var page = $(this).attr('p');
        loadData(page);

    });

    $('#go_btn').live('click', function () {
        var page = parseInt($('.goto').val());
        var no_of_pages = parseInt($('.total').attr('a'));
        if (page != 0 && page <= no_of_pages) {
            loadData(page);
        } else {
            alert('Enter a PAGE between 1 and ' + no_of_pages);
            $('.goto').val("").focus();
            return false;
        }

    });
});
