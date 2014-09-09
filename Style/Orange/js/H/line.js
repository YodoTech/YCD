// JavaScript Document
;(function($) {
    $('#lineOpen').mouseover(function() {
        var onService_panel = $('#onService_panel');
        onService_panel.animate({
            right: 0
        }, function() {});
        $(this).hide();
    });
    $('#lineClose').click(function() {
        var onService_panel = $('#onService_panel');
        onService_panel.animate({
            right: -102
        });
        onService_panel.find('.line_tab').fadeOut(100);
        $('#lineOpen').show();

    });
    $('.line_icon').click(function() {
        $('.line_tab').fadeOut(100);
        var onclickId = $(this).attr('id');
        switch (onclickId) {
            case 'line_tel_icon':
                $.jBox('iframe:http://qiao.baidu.com/v3/?module=default&controller=im&action=index&ucid=7743359&type=n&siteid=5440985', {
                    top: '10%',
                    border: 1,
                    title: '在线咨询',
                    width: 800,
                    height: 650,
                    buttons: {},
                    iframeScrolling: 'no',
                    showScrolling: false
                });
                $('#jbox-content').css('overflow-y', 'hidden');
                break;
            case 'line_qq_icon':
                $('#lineQQTab').fadeIn(100);
                break;
        }
    });
    $('#onService_panel .tab_close').click(function() {
        $(this).parents('.line_tab').hide();
    });
})(jQuery);