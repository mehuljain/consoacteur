$(document).ready(function () {
    if ($.browser.msie) {
        $('#menu ul.navmenu li:last-child .menutop').css('border-width', '0 1px 1px;')
    }

    $('.toggle:not(.toggle-open)').addClass('toggle-closed').parents('li').children('ul').hide();

    $('.toggle').click(function () {
        alert('Toggle');
        if ($(this).hasClass('toggle-open')) {
            $(this).removeClass('toggle-open').addClass('toggle-closed').empty('').append('+').parents('li').children('ul').slideUp(250);
            $(this).parent('.menutop').removeClass('menutop-open').addClass('menutop-closed');
        } else {
            $(this).parent('.menutop').removeClass('menutop-closed').addClass('menutop-open');
            $(this).removeClass('toggle-closed').addClass('toggle-open').empty('').append('&ndash;').parents('li').children('ul').slideDown(250);
        }
    })

    $(function () {
        var path = location.pathname.substring(1);
        alert('PAth:' + path);
        if (path)
            $('#menu a[href$="' + path + '"]').addClass('current');
    });


});
