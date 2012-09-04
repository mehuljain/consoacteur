$(document).ready(function () {

    $('#star-rating div.ratings_stars').hover(
        // Handles the mouseover
        function () {
            $(this).prevAll().andSelf().addClass('ratings_over');
            //$(this).nextAll().removeClass('ratings_vote');
        },
        // Handles the mouseout
        function () {
            $(this).prevAll().andSelf().removeClass('ratings_over');

        }
    );

    $('#star-rating div.ratings_stars').bind('click', function () {
        //$(this).prevAll().andSelf().addClass('ratings_vote');
        var rating = $(this).attr("id");
        $("input[id=rating]").val(rating);
        //$(this).attr("id").prevAll().andSelf().addClass('ratings_vote');
        //$("#"+rating+).prevAll().andSelf().addClass('ratings_vote');
        $(this).prevAll().andSelf().addClass('ratings_vote');
        $(this).nextAll().removeClass('ratings_vote');
    });

});
