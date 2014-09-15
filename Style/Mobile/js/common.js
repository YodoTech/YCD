;(function($){
// console.log($.fn.jquery);
var MY_COMMON_OP = {
    // 提示框
    alert: function(msg, fn) {
        var $alert = $('#sys-alert');
        var callback = function(f) {
            if ($.type(f)=='function') {
                f();
            } else {
                $.noop;
            }
        };
        if ($alert.length == 0) {
            window.alert(msg);
            callback(fn);
        } else {
            $alert
                .find('p').html(msg).end()
                .find('a').off().on('click', function() {
                    $alert.popup('close');
                    callback(fn);
                }).end()
                .popup('open');
        }
    },
    // confirm框
    confirm: function(c, fn1, fn2) {
    	var $confirm = $('#sys-confirm');
        var callback = function(f) {
            if ($.type(f)=='function') {
                f();
            } else {
                $.noop;
            }
        };
    	$confirm
    		.find('p').html(c).end()
    		.find('a:eq(0)').off().on('click', function() {
    			$confirm.popup('close');
    			callback(fn1);
    			return false;
    		}).end()
    		.find('a:eq(1)').off().on('click', function() {
    			$confirm.popup('close');
    			callback(fn2);
    			return false;
    		}).end()
    		.popup('open');
    },
    //打开loading组件
    //text(string): 加载提示文字
    //str(string): load的背景颜色样式(取值:a,b,c,d)
    //flag(boolean): 背景是否透明(取值:true透明,false不透明)
    loadStart: function(text, str, flag) {
        if (!text) {
            text = '加载中...';
        }
        $('.ui-loader h1').html(text);
        var _width = window.innerWidth;
        var _height = window.innerHeight;
        var htmlstr = '<div style="width:'+_width+'px;height:'+_height+'px;position:fixed;top:0px;left:0px;opacity:0.1;z-index:99999" class="loader-bg"></div>';
        $('body').append(htmlstr);
        if (flag) {
            $('.ui-loader').removeClass('ui-loader-verbose').addClass('ui-loader-default');
        } else {
            $('.ui-loader').removeClass('ui-loader-default').addClass('ui-loader-verbose');
        }
        var cla = 'ui-body-' + str;
        $('html').addClass('ui-loading');
        var arr = $('.ui-loader').attr('class').split(' ');
        var reg = /ui-body-[a-f]/;
        for (var i in arr) {
            if (reg.test(arr[i])) {
                $('.ui-loader').removeClass(arr[i]);
            }
        }
        $('.ui-loader').addClass(cla);
    },
    //结束loading组件
    loadStop: function() {
        $('html').removeClass('ui-loading');
        $('.loader-bg').remove();
    }
};
window.MY_COMMON_OP = MY_COMMON_OP;
})(jQuery);