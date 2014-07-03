<?php
// 本类由系统自动生成，仅供测试用途
class BorrowdetailAction extends MCommonAction {

    public function index(){
		$this->assign("bid",intval($_GET['id']));
		$this->display();
    }

    public function borrowdetail(){
		$pre = C('DB_PREFIX');
		$borrow_id = intval($_GET['id']);
		$list = getBorrowInvest($borrow_id,$this->uid);
		
		$this->assign("list",$list);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

    public function repayment(){
		$pre = C('DB_PREFIX');
		$borrow_id = intval($_POST['bid']);
		$sort_order = intval($_POST['sort_order']);
		$vo = M("borrow_info")->field('id')->where("id={$borrow_id} AND borrow_uid={$this->uid}")->find();
		if(!is_array($vo)) ajaxmsg("数据有误",0);
		$res = borrowRepayment($borrow_id,$sort_order);
		//$this->display("Public:_footer");
		if(true===$res) ajaxmsg();
		elseif(!empty($res)) ajaxmsg($res,0);
		else ajaxmsg("还款失败，请重试或联系客服",0);
    }

}