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

		$this->assign('guofubao_config',$payconfig['guofubao']);
		$this->assign('ips_config',$payconfig['ips']);
		$this->assign('chinabank_config',$payconfig['chinabank']);
		$this->assign('baofoo_config', $payconfig['baofoo']);
		$this->assign('shengpay_config', $payconfig['shengpay']);
		$this->assign('tenpay_config', $payconfig['tenpay']);
		$this->assign('ecpss_config', $payconfig['ecpss']);
        $this->display();
    }
    public function save()
    {
		FS("payconfig",$_POST['pay'],"Webconfig/");
		$this->success("操作成功",__URL__."/index/");
    }
}
?>