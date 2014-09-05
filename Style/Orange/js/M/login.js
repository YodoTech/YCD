var arrBox = new Array();
arrBox["dvUser"] = "请填写用户名或者邮件";
arrBox["dvCode"] = "请按照图片显示内容输入验证码。";
arrBox["dvPwd"] = "请填写您的密码";

var arrWrong = new Array();
arrWrong["dvUser"] = "请输入用户名。";
arrWrong["dvCode"] = "请输入验证码！";
arrWrong["dvPwd"] = "请输入密码！";

arrWrong["login"] = "用户名或密码填写错误！";

arrWrong["code"] = "用户名或密码填写错误！";
var arrOk = new Array();

arrOk["dvUser"] = "&nbsp;";
arrOk["dvPwd"] = "&nbsp;";
arrOk["dvCode"] = "&nbsp;";

function Init() {

    $('#txtUser').click(function() { ClickBox("dvUser"); });
    $('#txtUser').blur(function() { BlurEmail(); });

    $('#txtCode').click(function() { ClickBox("dvCode"); });
    $('#txtCode').blur(function() { BlurCode(); });

    $('#txtPwd').click(function() { ClickBox("dvPwd"); });
    $('#txtPwd').blur(function() { BlurPwd(); });

    var title = $('#txtUser').attr("title");
    $('#txtUser').val(title).css("color", "#8D8C8C").click(function() {
        if ($(this).val() == title)
        { $(this).val("").css("color", "#000"); }
    }).blur(function() {
        if ($(this).val().length < 1)
        { $(this).val(title).css("color", "#8D8C8C"); }
    });
}

function BlurCode() {

    var txt = "#txtCode";
    var td = "#dvCode";
    var pat = new RegExp("^[\\da-z]{4}$", "i");
    var str = $(txt).val();
    if (pat.test(str)) {
        $("#dvCode").html(GetP("reg_ok", arrOk["dvCode"]));
    }
    else {
        $(td).html(GetP("reg_wrong", arrWrong["dvCode"]));
    }
}

function strLength(as_str){
		return as_str.replace(/[^\x00-\xff]/g, 'xx').length;
}
function isLegal(str){
	if(/[!,#,$,%,^,&,*,?,~,\s+]/gi.test(str)) return false;
	return true;
}

function BlurEmail() {
    var txt = "#txtUser";
    var td = "#dvUser";
    //var pat1 = new RegExp("^[\\d\\.a-z_A-Z]{4,20}$", "i");
	var pat1 = new RegExp("^[\\d\\.a-z_A-Z]{4,20}|[\u4e00-\u9fa5]$", "i");
    var str = $(txt).val();
    if (str == $(txt).attr("title")) str = "";
    var pat2 = new RegExp("^[\\w-]+(\\.[\\w-]+)*@[\\w-]+(\\.[\\w-]+)+$", "i");
	strlen = strLength(str);
    if (!isLegal(str) || strlen<4 || strlen>20) {
        $(td).html(GetP("reg_wrong", arrWrong["dvUser"]));
        return;
    }
    $(td).html(GetP("reg_ok", arrOk["dvUser"]));
}

function BlurPwd() {
    var txt = "#txtPwd";
    var td = "#dvPwd";
    var pat = new RegExp("^.{6,}$", "i");
    var str = $(txt).val();
    if (pat.test(str)) {
        //格式正确
        $(td).html(GetP("reg_ok", arrOk["dvPwd"]));
    }
    else {
        $(td).html(GetP("reg_wrong", arrWrong["dvPwd"]));
    }
}

(function($) {
    $(function() {
        Init();
        //$("#txtUser").focus();
        $("#form1").keypress(
        function(e) {
            if (e.keyCode == "13")
                $("#btnReg").click();
        });
    });
})(jQuery);

function ClickBox(id) {
    var ele = '#' + id;
    $(ele).html(GetP("reg_info", arrBox[id]));
}

function GetP(clsName, c) { return "<div class='" + clsName + "'>" + c + "</div>"; }

function LoginSubmit(ctrl) {

    $(ctrl).unbind("click");
    var arrTds = new Array("#dvUser", "#dvPwd", "#dvCode");
    BlurEmail();
    BlurPwd();
    BlurCode();
    for (var i = 0; i < arrTds.length; i++) {
        if ($(arrTds[i]).html().indexOf('reg_wrong') > -1) {
            $(ctrl).click(function() { LoginSubmit(this); });
            return false;
        }
    }
    var keep = 0;
    if ($("#states").attr("checked") == true) {
        keep = 1;
    }
	$.jBox.tip("登陆中......",'loading');
	
	$.ajax({
		url: curpath+"/actlogin/",
		data: {"sUserName": $("#txtUser").val(),"sPassword": $("#txtPwd").val(),"sVerCode": $("#txtCode").val(),"Keep": $(":checkbox#loginstate:checked").val()},
		timeout: 5000,
		cache: false,
		type: "post",
		dataType: "json",
		success: function (d, s, r) {
			if(d){
				if(d.status==0){
					$.jBox.tip(d.message,"tip");	
				}else{
                    var redirect_uri = $('#redirect_uri').val();
                    if (redirect_uri=='') {
                        redirect_uri = '/member/';
                    } else {
                        redirect_uri = decodeURIComponent(redirect_uri);
                    }
                    window.location.href=redirect_uri;
				}
			}
		}
	});
}
