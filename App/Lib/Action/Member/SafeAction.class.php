<?php
// 本类由系统自动生成，仅供测试用途
class SafeAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function email(){
		$sq = M('member_safequestion')->find($this->uid);
		$email = M('members')->getFieldById($this->uid,'user_email');
		$this->assign("sq",$sq);
		$this->assign("email",$email);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

    public function idcard(){
		$ids = M('members_status')->getFieldByUid($this->uid,'id_status');
		if($ids==1){
			$vo = M("member_info")->field('idcard,real_name')->find($this->uid);
			$this->assign("vo",$vo);
			$data['html'] = $this->fetch();
		}
		else  $data['html'] = '<script type="text/javascript">alert("您还未完成身份验证，请先进行实名认证");window.location.href="'.__APP__.'/member/verify?id=1#fragment-3";</script>';
		exit(json_encode($data));
    }

    public function safequestion(){
		$sqs = M('members_status')->getFieldByUid($this->uid,'safequestion_status');
		if($sqs==0){
			$data['html'] = '<script type="text/javascript">alert("您还未设置安全问题，请先设置安全问题");window.location.href="'.__APP__.'/member/verify#fragment-6";</script>';
		}else{
			$sq = M('member_safequestion')->find($this->uid);
			$this->assign("sq",$sq);
			$this->assign("userphone",M('members')->getFieldById($this->uid,'user_phone'));
			$data['html'] = $this->fetch();
		}
		exit(json_encode($data));
    }
	public function changesafe(){
		$map['answer1'] = text($_POST['a1']);
		$map['answer2']  = text($_POST['a2']);
		$map['uid']  = $this->uid;
		$c = M('member_safequestion')->where($map)->count('uid');
		if($c==0) ajaxmsg('',0);
		else{
			session('temp_safequestion',1);
			ajaxmsg();
		}
	}
	public function changesafeact(){
		$is_can = session('temp_safequestion');
		if($is_can==1){
			$data['uid'] = $this->uid;
			$data['question1'] = text($_POST['q1']);
			$data['question2'] = text($_POST['q2']);
			$data['answer1'] = text($_POST['a1']);
			$data['answer2'] = text($_POST['a2']);
			$newid = M('member_safequestion')->save($data);
			if($newid){
				session('temp_safequestion',NULL);
				ajaxmsg();
			}
			else ajaxmsg('',0);
		}else{
			ajaxmsg('',0);
		}
	
	}

    public function cellphone(){
		$sq = M('member_safequestion')->find($this->uid);
		$phone = M('members')->getFieldById($this->uid,'user_phone');
		$this->assign("sq",$sq);
		$this->assign("phone",$phone);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	public function sendphonecode(){
		$r = Notice(3,$this->uid);
		if($r) ajaxmsg();
		else ajaxmsg('',0);
	}
	public function sendphonecodex(){
		$p = text($_POST['phone']);
		$c = M('members')->where("user_phone='{$p}'")->count('id');
		if($c>0) ajaxmsg('',2);
		$r = Notice(4,$this->uid,array('phone'=>$p));
		if($r) ajaxmsg();
		else ajaxmsg('',0);
	}
	public function changephone(){
		$vcode = text($_POST['code']);
		$pcode = is_verify($this->uid,$vcode,4,10*60);
		if($pcode){
			session('temp_phone',1);
			ajaxmsg();
		}
		else ajaxmsg('',0);
	}
	public function changephoneact(){
		$xs = session('temp_phone');
		$vcode = text($_POST['code']);
		$pcode = is_verify($this->uid,$vcode,5,10*60);
		if($pcode&&$xs==1){
			$newid = M('members')->where("id={$this->uid}")->setField('user_phone',text($_POST['phone']));
			session('temp_phone',NULL);
			if($newid) ajaxmsg();
			else  ajaxmsg('',0);
		}
		else ajaxmsg('',0);
	}
	
	
	public function sendemailtov(){
		$r = Notice(5,$this->uid);
		if($r) ajaxmsg();
		else ajaxmsg('',0);
	}
	
	public function doemailchangephone(){
		$code = text($_POST['safecode']);
		$r = is_verify($this->uid,$code,6,10*60);
		if(!$r) ajaxmsg("",2);
		$map['answer1'] = text($_POST['qone']);
		$map['answer2']  = text($_POST['qtwo']);
		$map['uid']  = $this->uid;
		$c = M('member_safequestion')->where($map)->count('uid');
		if($c==0) ajaxmsg('',0);
		session('temp_phone',1);
		ajaxmsg();
	}
	
	
	public function sendverify(){
		$r = Notice(2,$this->uid);
		if($r) echo(1);
		else echo(0);
	}
	
	public function verifyep(){
		$pcode = is_verify($this->uid,text($_POST['pcode']),3,10*60);
		$ecode = is_verify($this->uid,text($_POST['ecode']),3,10*60);

		if($pcode && $ecode){
			session('temp_safequestion',1);
			ajaxmsg();
		}else{
			ajaxmsg('',0);
		}
	}
	
	

}