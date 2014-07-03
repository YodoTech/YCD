var arrBox = new Array();
arrBox["dvEmail"] = "*&nbsp;请填写真实的电子邮件地址。";
arrBox["dvUser"] = "*&nbsp;4-20个字母、数字、不可以用汉字。";
arrBox["dvPwd"] = "*&nbsp;6-16个字母、数字、下划线。";
arrBox["dvRepwd"] = "*&nbsp;请再一次输入您的密码。";
arrBox["dvCode"] = "*&nbsp;请按照图片显示内容输入验证码。";

var arrWrong = new Array();
arrWrong["dvEmail"] = "*&nbsp;请输入电子邮件。";
arrWrong["dvUser"] = "*&nbsp;请输入用户名。";
arrWrong["dvPwd"] = "*&nbsp;请输入密码。";
arrWrong["dvRepwd"] = "*&nbsp;未输入或两次输入密码不同。";

arrWrong["dvCode"] = "*&nbsp;请输入验证码。";

var arrOk = new Array();
arrOk["dvEmail"] = "*&nbsp;电子邮件地址可用。";
arrOk["dvUser"] = "*&nbsp;用户名可用。";
arrOk["dvPwd"] = "*&nbsp;密码格式正确。";
arrOk["dvRepwd"] = "*&nbsp;密码格式正确。";
arrOk["dvCode"] = "*&nbsp;验证码正确。";


function Init() {
    $('#txtEmail').click(function() { ClickBox("dvEmail"); });
    $('#txtUser').click(function() { ClickBox("dvUser") });
    $('#txtPwd').click(function() { ClickBox("dvPwd") });
    $('#txtRepwd').click(function() { ClickBox("dvRepwd") });
    $('#txtCode').click(function() { ClickBox("dvCode") });

    $('#txtEmail').blur(function() { BlurEmail(); });
    $('#txtUser').blur(function() { BlurUName(); });
    $('#txtPwd').blur(function() { BlurPwd(); });
    $('#txtRepwd').blur(function() { BlurRepwd(); });
    $('#txtCode').blur(function() { BlurCode(); });

}

$(document).ready(
function() {
    Init();
    $("#txtEmail").focus();
    $("#Img1").click(function() { RegSubmit(this); });
    $("#txtCode").keypress(
    function(e) {
        if (e.keyCode == "13")
            $("#Img1").click();
    });
});

function strLength(as_str){
		return as_str.replace(/[^\x00-\xff]/g, 'xx').length;
}
function isLegal(str){
	if(/[!,#,$,%,^,&,*,?,~,\s+]/gi.test(str)) return false;
	return true;
}
function BlurUName() {
    var txt = "#txtUser";
    var td = "#dvUser";
    var pat = new RegExp("^[\\d|\\.a-z_A-Z|\\u4e00-\\u9fa5|\\x00-\\xff]$", "g");
    var str = $(txt).val();
    var strlen = strLength(str);
    if (isLegal(str) && strlen>=4 && strlen<=20) {
        $(td).html(GetP("reg_info", "<img style='margin:2px;' src='"+imgpath+"images/zhuce0.gif'/>&nbsp;正在检测用户名……"));
        $.ajax({
            type: "post",
            async: false,
            url: "/member/common/ckuser/",
			dataType: "json",
            data: {"UserName":str},
            timeout: 3000,
            success: AsyncUname
        });
    }
    else {
        $(td).html(GetP("reg_wrong", arrWrong["dvUser"]));
    }
}

function AsyncUname(data) {
    if (data.status == "1") {
        $("#dvUser").html(GetP("reg_ok", arrOk["dvUser"]));
    }
    else {
        $("#dvUser").html(GetP("reg_wrong", "<img style='margin:2px;' src='"+imgpath+"images/zhuce2.gif'/>&nbsp;此用户名已被注册。"));

    }

}

function BlurEmail() {
    var txt = "#txtEmail";
    var td = "#dvEmail";
    var pat = new RegExp("^[\\w-]+(\\.[\\w-]+)*@[\\w-]+(\\.[\\w-]+)+$", "i");
    var str = $(txt).val();
    if (pat.test(str)) {
        $(td).html(GetP("reg_info", "<img style='margin:2px;' src='"+imgpath+"images/zhuce0.gif'/>&nbsp;正在检测电子邮件地址……"));
        $.ajax({
            type: "post",
            async: false,
			dataType: "json",
            url: "/member/common/ckemail/",
            data: {"Email":str},
            timeout: 3000,
            success: AsyncEmail
        });
    }
    else { $(td).html(GetP("reg_wrong", arrWrong["dvEmail"])); }
}

function AsyncEmail(data) {
    if (data.status == "1") {
        $("#dvEmail").html(GetP("reg_ok", arrOk["dvEmail"]));
    }
     else {
       // $("#dvEmail").html(GetP("reg_wrong", "<img style='margin:2px;' src='"+imgpath+"images/zhuce2.gif'/>&nbsp;邮箱已经在本站注册<a href='javascript:;' onlick='getPassWord();'>取回密码？</a>"));
		$("#dvEmail").html(GetP("reg_wrong", "<img style='margin:2px;' src='"+imgpath+"images/zhuce2.gif'/>&nbsp;邮箱已经在本站注册<a href='javascript:getPassWord();'>取回密码？</a>"));
    }
}

function getPassWord() {
	window.location.href = "/member/common/getpassword/";
}

function BlurPwd() {
    var txt = "#txtPwd";
    var td = "#dvPwd";
    var pat = new RegExp("^.{6,20}$", "i");
    var str = $(txt).val();
    if (pat.test(str)) {
        //格式正确
        $(td).html(GetP("reg_ok", arrOk["dvPwd"]));
    }
    else {
        $(td).html(GetP("reg_wrong", arrWrong["dvPwd"]));
    }
}

function BlurRepwd() {
    var txt = "#txtRepwd";
    var td = "#dvRepwd";
    var str = $(txt).val();
    if (str == $("#txtPwd").val() && str.length > 5) {
        //格式正确
        $(td).html(GetP("reg_ok", arrOk["dvRepwd"]));
    }
    else {
        $(td).html(GetP("reg_wrong", arrWrong["dvRepwd"]));
    }
}
//检验 验证码
function BlurCode() {
    var txt = "#txtCode";
    var td = "#dvCode";
    var pat = new RegExp("^[\\da-z]{4}$", "i");
    var str = $(txt).val();
    if (pat.test(str)) {
        //格式正确
        $.post("/member/common/ckcode/", { Action: "post", Cmd: "CheckVerCode", sVerCode: str }, AsyncVerCode);
    }
    else {
        $(td).html(GetP("reg_wrong", arrWrong["dvCode"]));
    }
}

function AsyncVerCode(data) {
    if (data == "1") {
        $("#dvCode").html(GetP("reg_ok", arrOk["dvCode"]));
    }
    else {
        $("#dvCode").html(GetP("reg_wrong", "<img style='margin:2px;' src='"+imgpath+"images/zhuce2.gif'/>&nbsp;验证码填写错误！"));
    }
}

function ClickBox(id) {
    var ele = '#' + id;
    $(ele).html(GetP("reg_info", arrBox[id]));
}

function GetP(clsName, c) { return "<div class='" + clsName + "'>" + c + "</div>"; }

function RegSubmit(ctrl) {
    $(ctrl).unbind("click");
    var arrTds = new Array("#dvEmail", "#dvUser", "#dvPwd","#dvRepwd", "#dvCode");
    BlurEmail();
    BlurUName();
    BlurPwd();
    BlurCode();
    for (var i = 0; i < arrTds.length; i++) {
        if ($(arrTds[i]).html().indexOf('reg_wrong') > -1) {
            $(ctrl).click(function() { RegSubmit(this); });
            return false;
        }
    }
	
	var x = makevar(['txtEmail','txtUser','txtPwd']);
	$.jBox.tip("提交中......","loading");
	$.ajax({
		url: curpath+"/regaction/",
		data: x,
		timeout: 8000,
		cache: false,
		type: "post",
		dataType: "json",
		success: function (d, s, r) {
			if(d){
				if(d.status==0){
					$.jBox.tip(d.message,"fail");
					$(ctrl).click(function() { RegSubmit(this); });
				}else{
					$.jBox.tip("注册成功","success");
					$.jBox("get:"+curpath+"/regsuccess/", {
						title: "注册成功",
						width: "auto",
						buttons: {'关闭': true }
					});
				}
			}
		},
		complete:function(XMLHttpRequest, textStatus){
			if(textStatus!="success") $.jBox.tip('验证邮件发送失败，但注册成功，可以正常登陆', 'success');
			setTimeout('myrefresh()',5000); //指定3秒刷新
			//alert(textStatus);
		}
	});
}

function myrefresh()
{
	   window.location.href="/member/";
}
function AsyncReg(data) {
    Close_Dialog_AutoClose();
    if (data == "True") {
        suc();
    }
    else { }
}

function AsyncReg_Back() { window.location.href = "/member/"; }