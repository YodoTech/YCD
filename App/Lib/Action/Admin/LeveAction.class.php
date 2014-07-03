<?php
// 全局设置
class LeveAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$leveconfig = FS("Webconfig/leveconfig");

		$this->assign('leve',$leveconfig);
        $this->display();
    }
    public function save()
    {
		FS("leveconfig",$_POST['leve'],"Webconfig/");
		$this->success("操作成功",__URL__."/index/");
    }
}
?>