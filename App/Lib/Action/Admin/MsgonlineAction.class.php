<?php
// 全局设置
class MsgonlineAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$msgconfig = FS("Webconfig/msgconfig");
		$str = 'http://service.winic.org/webservice/public/remoney.asp?uid='.urlencode(auto_charset($msgconfig['sms']['user'],"utf-8","gbk")).'&pwd='.urlencode($msgconfig['sms']['pass']);
		$d = file_get_contents($str, false);
		if (false === $d) {
			$d = '远程获取数据出错';
		} elseif($d < 0) {
			$d = '用户名或密码错误';
		} else {
			$d = '￥'.$d;
		}
		$this->assign('d',$d);
		$this->assign('stmp_config',$msgconfig['stmp']);
		$this->assign('sms_config',$msgconfig['sms']);
        $this->display();
    }
    public function save()
    {
		FS("msgconfig",$_POST['msg'],"Webconfig/");
		$this->success("操作成功",__URL__."/index/");
    }
	
	
    public function templet()
    {
		$emailTxt = FS("Webconfig/emailtxt");
		$smsTxt = FS("Webconfig/smstxt");
		$msgTxt = FS("Webconfig/msgtxt");

		$this->assign('emailTxt',de_xie($emailTxt));
		$this->assign('smsTxt',de_xie($smsTxt));
		$this->assign('msgTxt',de_xie($msgTxt));
        $this->display();
    }
	
    public function templetsave()
    {
		FS("emailtxt",$_POST['email'],"Webconfig/");
		FS("smstxt",$_POST['sms'],"Webconfig/");
		FS("msgtxt",$_POST['msg'],"Webconfig/");
		$this->success("操作成功",__URL__."/templet/");
    }
}
?>