<?php
// 全局设置
class AgeAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$ageconfig = FS("Webconfig/ageconfig");

		$this->assign('leve',$ageconfig);
        $this->display();
    }
    public function save()
    {
		FS("ageconfig",$_POST['leve'],"Webconfig/");
		$this->success("操作成功",__URL__."/index/");
    }
}
?>