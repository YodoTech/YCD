var arrWrong = new Array();
arrWrong["dvUser"] = "请输入用户名。";
arrWrong["dvPwd"] = "请输入密码！";
arrWrong["dvCode"] = "请输入验证码！";

function BlurCode() {
    var txt = "#txtCode";
    var td = "#dvCode";
    var pat = new RegExp("^[\\da-z]{4}$", "i");
    var str = $(txt).val();
    if (pat.test(str)) {
        return true;
    }
    return false;
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
    //var pat1 = new RegExp("^[\\d\\.a-z_A-Z]{4,20}$", "i");
	var pat1 = new RegExp("^[\\d\\.a-z_A-Z]{4,20}|[\u4e00-\u9fa5]$", "i");
    var str = $(txt).val();
    if (str == $(txt).attr("title")) str = "";
    var pat2 = new RegExp("^[\\w-]+(\\.[\\w-]+)*@[\\w-]+(\\.[\\w-]+)+$", "i");
	strlen = strLength(str);
    if (!isLegal(str) || strlen<4 || strlen>20) {
        return false;
    }
    return true;
}

function BlurPwd() {
    var txt = "#txtPwd";
    var pat = new RegExp("^.{6,}$", "i");
    var str = $(txt).val();
    if (pat.test(str)) {
        return true;
    }
    return false;
}

function GetP(clsName, c) { return "<div class='" + clsName + "'>" + c + "</div>"; }

function LoginSubmit(ctrl) {
    var $this = $(ctrl);
    var $wrong = $('#txtWrong');
    $this.unbind("click");
    var state = true;
    if (state && !BlurEmail()) {
        $wrong.html(GetP("reg_wrong", arrWrong["dvUser"]));
        state = false;
    }
    if (state && !BlurPwd()) {
        $wrong.html(GetP("reg_wrong", arrWrong["dvPwd"]));
        state = false;
    }
    if (state && !BlurCode()) {
        $wrong.html(GetP("reg_wrong", arrWrong["dvCode"]));
        state = false;
    }
    if (!state) {
        $this.click(function() { LoginSubmit(this); });
        return false;
    }

    MY_COMMON_OP.loadStart('登陆中......', 'b', false);
	$.ajax({
		url: curpath+"/actlogin/",
		data: {"sUserName": $("#txtUser").val(),"sPassword": $("#txtPwd").val(),"sVerCode": $("#txtCode").val()},
		timeout: 5000,
		cache: false,
		type: "post",
		dataType: "json",
		success: function (d, s, r) {
            MY_COMMON_OP.loadStop();
			if(d) {
				if (d.status == 0) {
                    $this.click(function() { LoginSubmit(this); });
                    MY_COMMON_OP.alert(d.message);
				} else {
                    var redirect_uri = $('#redirect_uri').val();
                    if (redirect_uri == '') {
                        redirect_uri = '/member/';
                    } else {
                        redirect_uri = decodeURIComponent(redirect_uri);
                    }
                    window.location.href=redirect_uri;
				}
			}
		}
	});
    return true;
}