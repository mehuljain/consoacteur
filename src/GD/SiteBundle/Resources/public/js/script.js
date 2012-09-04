$(document).ready(function () {

    if ($.browser.msie) {
        $('#lightbox').css({
            opacity:0.8, width:$(document.body).width(), height:$(document.body).height()
        });
    } else {
        $('#lightbox').css({
            opacity:0.8, width:$(document).width(), height:$(document).height()
        });
    }
    
    $("a#inscription").click(function (event) {
        event.preventDefault();
        $("#lightbox, .popup").fadeIn(300);
    });
    $("#lightbox").click(function () {
        $("#lightbox, .popup").fadeOut(300);
    });
    $("#close01").click(function () {
        $("#lightbox, .popup").fadeOut(300);
    });

    $("a#newsletter").click(function(event) {
        event.preventDefault();
        $("#lightbox, .newsl_popup").fadeIn(300);
    });
    $("#lightbox").click(function () {
        $("#lightbox, .newsl_popup").fadeOut(300);
    });
    $("#close02").click(function () {
        $("#lightbox, .newsl_popup").fadeOut(300);
    });

    $("a#fbk").click(function () {
        var id = $('#hidden').val();
        $("input[id=mid]").val(id);
        $("#lightbox, .feedback").fadeIn(300);
    });
    $("#lightbox").click(function () {
        $("#lightbox, .feedback").fadeOut(300);
    });
    $("#close04").click(function () {
        $("#lightbox, .feedback").fadeOut(300);
    });

    $(".lang").click(function () {
       $(".geography_selector").slideToggle('fast');
       //event.stopPropagation(); // To prevent the $('html').click to toggle it
    });
    
    $("#lightbox").click(function () {
        $("#lightbox, .authentication").fadeOut(300);
    });
    $("#close05").click(function () {
        $("#lightbox, .authentication").fadeOut(300);
    });
    
    $(".authentication a.signIn").click(function () {
        event.preventDefault();
        $(".authentication .login form").fadeIn(300);
    });
    
    $("#cb_slide_down").click(function () {
        $("div.cashback div.module ul li").slideDown("slow");
    });


    function megaHoverOver() {
        $(this).find(".sub").stop().fadeTo('fast', 1).show();
        $(this).find(".sub").each(function () {
            $(this).parent().find('a.menu').addClass('hassub');

        });
    }

    function megaHoverOut() {
        $(this).find('a.menu').removeClass('hassub');
        $(this).find(".sub").stop().fadeTo('fast', 0, function () {
            $(this).hide();
        });
    }


    var config = {
        sensitivity:2, // number = sensitivity threshold (must be 1 or higher)
        interval:100, // number = milliseconds for onMouseOver polling interval
        over:megaHoverOver, // function = onMouseOver callback (REQUIRED)
        timeout:500, // number = milliseconds delay before onMouseOut
        out:megaHoverOut // function = onMouseOut callback (REQUIRED)
    };

    $("ul#topnav li .sub").css({'opacity':'0'});
    $("ul#topnav li").hoverIntent(config);
    $("li:has(.sub)").each(function () {
       // $(this).find(".menu span").css({'padding-right':'27px'});
        //$(this).find(".menu span").append("<img class='sub_menu' src='{{ asset('bundles/gdsite/images/menu_down.png') }}' />");
    });
    
    $("li:has(.sub)").hover(
        function () {
            //$(this +' span').html('<img src=\"\" />');
            $('.menu').children('span').children('img').attr('src',window.menuUpUrl);

        },
        function () {
            //$(this +' span').html('<img src=\"\" />');
            $('.menu').children('span').children('img').attr('src',window.menuDownUrl);

        }
    ); 
});
