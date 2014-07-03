<?php
// 本类由系统自动生成，仅供测试用途
class RepaymentAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function summary(){
		$pre = C('DB_PREFIX');
		
		$this->assign("borrow_interest",$borrow_interest);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
}