﻿var arrBox = new Array();
arrBox["dvEmail"] = "请填写真实的电子邮件地址。";
arrBox["dvUser"] = "4-20个字母、数字、不可以用汉字。";
arrBox["dvPwd"] = "6-16个字母、数字、下划线。";
arrBox["dvRepwd"] = "请再一次输入您的密码。";
arrBox["dvRole"] = "请选择角色。";
arrBox["dvCode"] = "请按照图片显示内容输入验证码。";
arrBox["dvAgree"] = "请同意我们的条款。";

var arrWrong = new Array();
arrWrong["dvEmail"] = "请输入电子邮件。";
arrWrong["dvUser"] = "请输入用户名。";
arrWrong["dvPwd"] = "请输入密码。";
arrWrong["dvRepwd"] = "未输入或两次输入密码不同。";
arrWrong["dvRole"] = "请选择角色。";
arrWrong["dvCode"] = "请输入验证码。";
arrWrong["dvAgree"] = "请同意我们的条款。";

var arrOk = new Array();
arrOk["dvEmail"] = "电子邮件地址可用。";
arrOk["dvUser"] = "用户名可用。";
arrOk["dvPwd"] = "密码格式正确。";
arrOk["dvRepwd"] = "密码格式正确。";
arrOk["dvRole"] = "&nbsp;";
arrOk["dvCode"] = "验证码正确。";
arrOk["dvAgree"] = "&nbsp;";

function Init() {
    $('#txtEmail').click(function() { ClickBox("dvEmail"); });
    $('#txtUser').click(function() { ClickBox("dvUser") });
    $('#txtPwd').click(function() { ClickBox("dvPwd") });
    $('#txtRepwd').click(function() { ClickBox("dvRepwd") });
    $(':radio[name=txtRole]').click(function() { BlurRole(); });
    $('#txtCode').click(function() { ClickBox("dvCode") });
    $(':checkbox#txtAgree').click(function() { BlurAgree(); });

    $('#txtEmail').blur(function() { BlurEmail(); });
    $('#txtUser').blur(function() { BlurUName(); });
    $('#txtPwd').blur(function() { BlurPwd(); });
    $('#txtRepwd').blur(function() { BlurRepwd(); });
    $('#txtCode').blur(function() { BlurCode(); });
}

$(function() {
    Init();
    $("#txtEmail").focus();
    $("#Img1").click(function() { RegSubmit(this); });
    $("#txtCode").keypress(
    function(e) {
        if (e.keyCode == "13")
            $("#Img1").click();
    });
    //角色选择
    $("li.zhuceym3R_1").on("click", "a", function() {
        var a = $(this).parent().index();
        $(this).parent().addClass("cur").siblings().removeClass("cur");
        $(":radio[name=txtRole]").eq(a).prop('checked', true).trigger("click");
        return false;
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
        $("#dvUser").html(GetP("reg_wrong", "此用户名已被注册。"));

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
       // $("#dvEmail").html(GetP("reg_wrong", "邮箱已经在本站注册<a href='javascript:;' onlick='getPassWord();'>取回密码？</a>"));
		$("#dvEmail").html(GetP("reg_wrong", "邮箱已经在本站注册<a href='javascript:getPassWord();'>取回密码？</a>"));
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

function BlurRole() {
    var txt = ":radio[name=txtRole]:checked";
    var td = "#dvRole";
    if ($(txt).length === 0) {
        $(td).html(GetP("reg_wrong", arrWrong["dvRole"]));
    } else {
        //格式正确
        $(td).html(GetP("reg_ok", arrOk["dvRole"]));
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

function BlurAgree() {
    var txt = ":checkbox#txtAgree:checked";
    var td = "#dvAgree";
    if ($(txt).length === 0) {
        $(td).html(GetP("reg_wrong", arrWrong["dvAgree"]));
    } else {
        //格式正确
        $(td).html(GetP("reg_ok", arrOk["dvAgree"]));
    }
}

function AsyncVerCode(data) {
    if (data == "1") {
        $("#dvCode").html(GetP("reg_ok", arrOk["dvCode"]));
    }
    else {
        $("#dvCode").html(GetP("reg_wrong", "验证码填写错误！"));
    }
}

function ClickBox(id) {
    var ele = '#' + id;
    $(ele).html(GetP("reg_info", arrBox[id]));
}

function GetP(clsName, c) { return "<div class='mt10 ml10 red " + clsName + "'>" + c + "</div>"; }

function RegSubmit(ctrl) {
    $(ctrl).unbind("click");
    var arrTds = new Array("#dvEmail", "#dvUser", "#dvPwd","#dvRepwd", "#dvRole", "#dvCode", "#dvAgree");
    BlurEmail();
    BlurUName();
    BlurPwd();
    BlurRole();
    BlurCode();
    BlurAgree();
    for (var i = 0; i < arrTds.length; i++) {
        if ($(arrTds[i]).html().indexOf('reg_wrong') > -1) {
            $(ctrl).click(function() { RegSubmit(this); });
            return false;
        }
    }

	var x = makevar(['txtEmail','txtUser','txtPwd','txtRole']);
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
				if(d.status==0) {
					$.jBox.tip(d.message, 'fail');
					$(ctrl).click(function() { RegSubmit(this); });
				} else {
                    if (d.message=='') {
                        myrefresh();
                    } else {
                        $.jBox.success(d.message, '提示', {
                            closed: function() {myrefresh();}
                        });
                    }
				}
			}
		},
		error:function(XMLHttpRequest, textStatus) {
			$.jBox.tip('textStatus', 'error');
			setTimeout('myrefresh()',3000);
		}
	});
}

function myrefresh()
{
    window.location.href="/member/common/register/";
}
function AsyncReg(data) {
    Close_Dialog_AutoClose();
    if (data == "True") {
        suc();
    }
    else { }
}

function AsyncReg_Back() { window.location.href = "/member/"; }