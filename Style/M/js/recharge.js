
function checkbank(ctrl) {
    var yh = $(ctrl).attr('id').split('_')[1];
    var ryh = "r_" + yh;
    $("#" + ryh).attr("checked", "checked");
    $("#s_selectbank").attr("src", bankimg+"images/bank/" + yh + "1_b.jpg");
    $("#pd_bank").val($(ctrl).attr('data'));

}

function submitform(ctrl) {

    var pd_bank = $("#pd_bank").val();
    var pa_MP = $("#pa_MP").val();
    submitform_gopay(ctrl, pd_bank);
}


function submitform_gopay(ctrl, bankCode) {
    if (BlurMoney()) {
        var tranAmt = $("#t_money").val();
        var url = "bankCode=" + bankCode;
        url += "&t_money=" + tranAmt;
		if(1==2&&bankCode=='CCB') window.open("/Pay/ccb?" + url);
		else if(bankCode=='tenpay') window.open("/Pay/tenpay?" + url);
		else window.open("/Pay/chinabank?" + url);
        //Open_Dialog();
    }
}


function submitform_credit(ctrl) {

    var pd_bank = $("#pd_bank").val();
    var pa_MP = $("#pa_MP").val();

    //gsyh(工商银行),zgyh(中国银行),jsyh(建设银行),zsyh(招商银行),jtyh(交通银行),ycyh(邮政储蓄),msyh(民生银行),gfyh(广发银行)
    //gdyh(光大银行),zxyh(中信银行),xyyh(兴业银行),payh(平安银行),hxyh(华夏银行),nyyh(农业银行)
    if (("chinabank,gsyh,zgyh,jsyh,zsyh,jtyh,ycyh,msyh,gfyh,gdyh,zxyh,xyyh,payh,hxyh,nyyh").indexOf(pd_bank) >= 0) {
        submitform_gopay(ctrl, pd_bank, pa_MP, 1);
        return;
    }

    submitform_yeepay(ctrl, pd_bank, pa_MP, 1);
}

function submitform_yeepay(ctrl, pd_bank, pa_MP, credit) {
    if (BlurMoney()) {
        var p2_Order = $("#p2_Order").val();
        var p3_Amt = $("#t_money").val();
        var p5_Pid = $("#p5_Pid").val();
        var p6_Pcat = $("#p6_Pcat").val();
        var p7_Pdesc = $("#p7_Pdesc").val();
        var p8_Url = $("#callback").val() + "pay/Callback.html";
        var p9_SAF = $("#p9_SAF").val();

        var url = "?p2_Order=" + p2_Order;
        url += "&p3_Amt=" + p3_Amt;
        url += "&p5_Pid=" + p5_Pid;
        url += "&p6_Pcat=" + p6_Pcat;
        url += "&p7_Pdesc=" + p7_Pdesc;
        url += "&p8_Url=" + p8_Url;
        url += "&p9_SAF=" + p9_SAF;
        url += "&pa_MP=" + pa_MP;
        url += "&pd_bank=" + pd_bank;
        if (credit == 1) url += "&credit=" + credit;
        window.open("/pay/go.html" + url);
        Open_Dialog();

    }
}

var arrBox = new Array();
arrBox["d_money"] = "<img style='margin:2px;' src='"+Himg+"images/zhuce1.gif'/>&nbsp;请输入正确的金额，最小充值金额50元。";
var arrWrong = new Array();
arrWrong["d_money"] = "<img style='margin:2px;' src='"+Himg+"images/zhuce2.gif'/>&nbsp请输入正确的金额，小数点后最多2位数。";
var arrWrong1 = new Array();
arrWrong1["d_money"] = "<img style='margin:2px;' src='"+Himg+"images/zhuce2.gif'/>&nbsp;输入的金额不能为空！";
var arrWrong2 = new Array();
arrWrong2["d_money"] = "<img style='margin:2px;' src='"+Himg+"images/zhuce2.gif'/>&nbsp;最低充值金额为1元！";
var arrWrong3 = new Array();
arrWrong3["d_money"] = "<img style='margin:2px;' src='"+Himg+"images/zhuce2.gif'/>&nbsp;充值金额必须大于0元！";

var arrOk = new Array();
arrOk["d_money"] = "<img style='margin:2px;' src='"+bankimg+"images/zhuce3.gif'/>";

function Init() {
    $('#t_money').click(function() { ClickBox("d_money"); });
    $('#t_money').blur(function() { BlurMoney(); });
}

function BlurMoney() {
    var txt = "#t_money";
    var td = "#d_money";
    var pat = /^[0-9]*(\.[0-9]{1,2})?$/;

    var str = $(txt).val();
    if (str == "") {
        $(td).html(GetP("charge_wrong", arrWrong1["d_money"]));
        return false;
    }
    var m = parseFloat(str);
    if (m <= 0) {
        $(td).html(GetP("charge_wrong", arrWrong3["d_money"]));
        return false;
    }

    //限制50最小金额
    //var m = parseInt(str);
	var m = parseFloat(str);
    //alert(m);
    if (m < 0.01) {
        $(td).html(GetP("charge_wrong", arrWrong2["d_money"]));
        return false;
    }
    if (pat.test(str)) {
        $(td).html(GetP("reg_ok", arrOk["d_money"]));
        return true;
    }
    else {
        $(td).html(GetP("charge_wrong", arrWrong["d_money"]));
        return false;
    }
}

function ClickBox(id) {
    var ele = '#' + id;
    $(ele).html(GetP("reg_info", arrBox[id]));
}
function GetP(clsName, c) {
    return "<div class='" + clsName + "'>" + c + "</div>";
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function Open_Dialog() {
    setTimeout('$("#tiplink").click()', 500); return;
    Init_Back();
    Show_Back();
    Init_Dialog();
    Show_Dialog();

}

function Init_Back() {
    var objDiv = "<div id='myBack' style='position: absolute;z-index: 2000;left: 0;top: 0;bottom: 0;background: black;display: none;opacity: 0.2;filter: progid:DXImageTransform.Microsoft.Alpha(opacity=20);-ms-filter: \"progid:DXImageTransform.Microsoft.Alpha(opacity=20)\" '></div>";
    var objIframe = "<iframe id='myIframe' style='position: absolute;z-index: 1999;left: 0;top: 0;bottom: 0;background: #000000;display: none;opacity: 0.2;filter: progid:DXImageTransform.Microsoft.Alpha(opacity=20);-ms-filter: \"progid:DXImageTransform.Microsoft.Alpha(opacity=20)\"'></iframe>";

    $("body").append(objDiv);
    $("body").append(objIframe);
    $('html').css('overflow', 'hidden');
}

function Show_Back() {
    var st = $(window).scrollTop();
    $("#myIframe").css('width', $(window).width());
    $("#myIframe").css('height', $(window).height() + st);
    $('#myIframe').show();
    $("#myBack").css('width', $(window).width());
    $("#myBack").css('height', $(window).height() + st);
    $('#myBack').show();
}

function Init_Dialog() {
    var Div_Confirm = "";
    Div_Confirm += "<div id='myDialog' style='height:180px;width:400px;border:1px solid #000000;background-color: #f6f6f6;position: absolute;display: none;z-index: 2001; '>";
    Div_Confirm += "<div style='height:30px;line-height:30px;background-color: #c60808;width:400px;color:#ffffff;font-weight: bold;'>友情提示：请不要关闭此页面</div>";
    Div_Confirm += "<div style='height:30px;line-height:30px;width:400px;font-weight: bold;text-align:left;'>&nbsp;&nbsp;<img style='height:20px;width:20px;' src='"+Himg+"images/zs1.gif' />&nbsp;&nbsp;您正在登录网上银行充值……</div>";
    Div_Confirm += "<div style='height:30px;line-height:30px;width:400px;font-weight: bold;text-align:left;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请在网上银行充值后选择：</div>";
    Div_Confirm += "<div style='height:60px;line-height:60px;width:400px;text-align:center;'>";
    Div_Confirm += "<input value=' ' onmouseover='this.style.cursor=\"hand\"' type='button' onclick='finish()' style='background-image:url("+Himg+"images/gofinish.jpg); width:96px; height:28px; border:none;float:left;margin-left:50px;margin-top:15px; '  />";
    Div_Confirm += "</div>";
    Div_Confirm += "<div style='height:30px;line-height:30px;width:400px;text-align:left;font-weight: bold;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;充值遇到问题？你可以：&nbsp;&nbsp;<a style='text-decoration:underline; font-weight:normal ; ' href='/account/recharge.html'>重新选择充值银行>></a>&nbsp;&nbsp;<a style='text-decoration:underline; font-weight:normal ; '  href='/service/i_question.html'>查看帮助</a></div>";
    Div_Confirm += "</div>";

    $("body").append(Div_Confirm);
}


function Show_Dialog() {
    setTimeout('$("#tiplink").click()', 500); return;
    var ww = $(window).width(), wh = $(window).height(), st = $(window).scrollTop(), bh = $("body").height();
    var left = ww / 2 - $("#myDialog").width() / 2;
    var top = st + wh / 2 - $("#myDialog").height() / 2;
    $("#myDialog").css('left', left).css('top', top);
    $("#myDialog").show();
}
