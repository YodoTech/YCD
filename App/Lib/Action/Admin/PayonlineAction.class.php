<?php
// 全局设置
class PayonlineAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$payconfig = FS("Webconfig/payconfig");

		$this->assign('chinabank_config',$payconfig['chinabank']);
		$this->assign('tenpay_config', $payconfig['tenpay']);
        $this->display();
    }
    public function save()
    {
		FS("payconfig",$_POST['pay'],"Webconfig/");
		$this->success("操作成功",__URL__."/index/");
    }
}
?>