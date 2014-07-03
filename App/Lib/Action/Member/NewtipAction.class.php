<?php
// 本类由系统自动生成，仅供测试用途
class NewtipAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function newtip(){
		$pre = C('DB_PREFIX');
		$sqs = M('members')->field('phone_status,user_phone')->find($this->uid);
		if(empty($sqs['phone_status'])) $data['html'] = '<script type="text/javascript">alert("您还未验证手机，请先验证");window.location.href="'.__APP__.'/member/verify";</script>';
		else{
			$vo = M('borrow_tip')->where("uid={$this->uid}")->find();
			$vo['borrow_type'] = ($vo['borrow_type']==3)?"流转标":"普通标";
			$this->assign('user_phone',$sqs['user_phone']);
			$this->assign('vo',$vo);
			$data['html'] = $this->fetch();
		}
		exit(json_encode($data));
    }
    public function savetip(){
		$duration = explode(",",text($_POST['loancycle']));
		$data['uid'] = $this->uid;
		$data['account_money'] = floatval($_POST['miniamount']);
		$data['borrow_type'] = intval($_POST['borrowkind']);
		$data['interest_rate'] = intval($_POST['interestrates']);
		$data['duration_from'] = intval($duration[0]);
		$data['duration_to'] = intval($duration[1]);
		$c = M('borrow_tip')->field('id')->where("uid={$this->uid}")->find();
		if(is_array($c)){
			$data['id'] = $c['id'];
			$newid = M('borrow_tip')->save($data);
			if($newid) ajaxmsg("修改成功",1);
			else ajaxmsg("修改失败，请重试",0);
		}
		else{
			$newid = M('borrow_tip')->add($data);
			if($newid) ajaxmsg("添加成功",1);
			else ajaxmsg("添加失败，请重试",0);
		}
	
	}
	public function removetip(){
		$id=intval($_POST['settingId']);
		$newid = M('borrow_tip')->where("uid={$this->uid} AND id={$id}")->delete();
		//$this->display("Public:_footer");
		if(false!==$newid) echo 1;
		else echo 0;
	}

}