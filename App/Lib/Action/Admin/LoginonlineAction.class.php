<?php
// 全局设置
class LoginonlineAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$loginconfig = FS("Webconfig/loginconfig");

		$this->assign('qq_config',$loginconfig['qq']);
		$this->assign('sina_config',$loginconfig['sina']);
		$this->assign('uc_config',$loginconfig['uc']);
		$this->assign('cookie_config',$loginconfig['cookie']);
        $this->display();
    }
    public function save()
    {
		FS("loginconfig",$_POST['login'],"Webconfig/");
		$this->success("操作成功",__URL__."/index/");
    }
}
?>