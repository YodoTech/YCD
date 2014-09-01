<?php
// 本类由系统自动生成，仅供测试用途
class VerifyAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function welcome(){
		$data['content'] = $this->fetch();
		exit(json_encode($data));
    }

    public function email(){
		$email = M('members')->field('user_email')->find($this->uid);
		$this->assign("email_status",M('members_status')->getFieldByUid($this->uid,'email_status'));
		$this->assign("email",M('members')->getFieldById($this->uid,'user_email'));
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

    public function emailvalided(){
		$status = M("members_status")->getFieldByUid($this->uid,'email_status');
		ajaxmsg('',$status);
    }

  	public function emailvsend1(){
		$status=Notice(8,$this->uid);
		if($status) ajaxmsg();
		else  ajaxmsg('',0);
    }
	
    public function emailvsend(){
		$data['user_email'] = text($_POST['email']);
		$newid = M('members')->where("id = {$this->uid}")->save($data);//更改邮箱，重新激活
		if($newid){
			$status=Notice(8,$this->uid);
			if($status) ajaxmsg('邮件已发送，请注意查收！',1);
			else ajaxmsg('邮件发送失败,请重试！',0);
		}else{
			 ajaxmsg('新邮件修改失败',2);
		}
    }

	public function ckemail(){
		$map['user_email'] = text($_POST['Email']);
		$count = M('members')->where($map)->count('id');
        
		if ($count>0) {
			$json['status'] = 0;
			exit(json_encode($json));
        } else {
			$json['status'] = 1;
			exit(json_encode($json));
        }
	}
    public function idcard(){
		$isid = M('members_status')->getFieldByUid($this->uid,'id_status');
		$this->assign("id_status",$isid);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

    public function saveid(){
		$isimg = session('idcardimg');
		$isimg2 = session('idcardimg2');
		$data['real_name'] = text($_POST['realname']);
		$data['idcard'] = text($_POST['idcard']);
		$data['up_time'] = time();
		/////////////////////////
		$data1['idcard'] = text($_POST['idcard']);
		$data1['up_time'] = time();
		$data1['uid'] = $this->uid;
		$data1['status'] = 0;
		$b = M('name_apply')->where("uid = {$this->uid}")->count('uid');
		if($b==1){
			M('name_apply')->where("uid ={$this->uid}")->save($data1);
		}else{
			M('name_apply')->add($data1);
		}
		////////////////////////
		if($isimg!=1) ajaxmsg("请先上传身份证正面图片",0);
		if($isimg2!=1) ajaxmsg("请先上传身份证反面图片",0);
		if(empty($data['real_name'])||empty($data['idcard']))  ajaxmsg("请填写真实姓名和身份证号码",0);
		$xuid = M('member_info')->getFieldByIdcard($data['idcard'],'uid');
		if($xuid>0 && $xuid!=$this->uid) ajaxmsg("此身份证号码已被人使用",0);
		$c = M('member_info')->where("uid = {$this->uid}")->count('uid');
		if($c==1){
			$newid = M('member_info')->where("uid = {$this->uid}")->save($data);
		}else{
			$data['uid'] = $this->uid;
			$newid = M('member_info')->add($data);
		}
		
		session('idcardimg',NULL);
		session('idcardimg2',NULL);
		if($newid){
			$ms=M('members_status')->where("uid={$this->uid}")->setField('id_status',3);
			if($ms==1){
				ajaxmsg();
			}else{
				$dt['uid'] = $this->uid;
				$dt['id_status'] = 3;
				M('members_status')->add($dt);
			}
			ajaxmsg();
		}
		else  ajaxmsg("保存失败，请重试",0);
    }

    public function safequestion(){
		$isid = M('members_status')->getFieldByUid($this->uid,'safequestion_status');
		$this->assign("safe_question",$isid);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	
	public function questionsave(){
		$data['question1'] = text($_POST['q1']);
		$data['question2'] = text($_POST['q2']);
		$data['answer1'] = text($_POST['a1']);
		$data['answer2'] = text($_POST['a2']);
		$data['add_time'] = time();
		$c = M('member_safequestion')->where("uid = {$this->uid}")->count('uid');
		if($c==1) $newid = M("member_safequestion")->where("uid={$this->uid}")->save($data);
		else{
			$data['uid'] = $this->uid;
			$newid = M('member_safequestion')->add($data);
		}
		if($newid){
			M('members_status')->where("uid = {$this->uid}")->setField('safequestion_status',1);
			ajaxmsg();
		}
		else  ajaxmsg("",0);
	}


    public function cellphone(){
		$isid = M('members_status')->getFieldByUid($this->uid,'phone_status');
		$this->assign("phone_status",$isid);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

    public function sendphone(){
		$smsTxt = FS("Webconfig/smstxt");
		$smsTxt=de_xie($smsTxt);
		$phone = text($_POST['cellphone']);
		$xuid = M('members')->getFieldByUserPhone($phone,'id');
		if($xuid>0 && $xuid<>$this->uid) ajaxmsg("",2);
		
		$code = rand_string($this->uid,6,1,2);
		$res = sendsms($phone,str_replace(array("#UserName#","#CODE#"),array(session('u_user_name'),$code),$smsTxt['verify_phone']));
		if($res){
			session("temp_phone",$phone);
			ajaxmsg();
		}
		else ajaxmsg("",0);
    }

    public function done(){
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

    public function validatephone(){
		$phonestatus = M('members_status')->getFieldByUid($this->uid,'phone_status');
		if($phonestatus==1) ajaxmsg("手机已经通过验证",1);
		if( is_verify($this->uid,text($_POST['code']),2,10*60) ){
			$updata['phone_status'] = 1;
			if(!session("temp_phone")) ajaxmsg("验证失败",0);
			
			$updata1['user_phone'] = session("temp_phone");
			$a = M('members')->where("id = {$this->uid}")->count('id');
			if($a==1) $newid = M("members")->where("id={$this->uid}")->save($updata1);
			else{
				M('members')->where("id={$this->uid}")->setField('user_phone',session("temp_phone"));
			}
			
			$updata2['cell_phone'] = session("temp_phone");
			$b = M('member_info')->where("uid = {$this->uid}")->count('uid');
			if($b==1) $newid = M("member_info")->where("uid={$this->uid}")->save($updata2);
			else{
				$updata2['uid'] = $this->uid;
				M('member_info')->add($updata2);
			}
			$c = M('members_status')->where("uid = {$this->uid}")->count('uid');
			if($c==1) $newid = M("members_status")->where("uid={$this->uid}")->save($updata);
			else{
				$updata['uid'] = $this->uid;
				$newid = M('members_status')->add($updata);
			}
			if($newid){
				ajaxmsg();
				
			}
			else  ajaxmsg("验证失败",0);
		}else{
			ajaxmsg("验证校验码不对，请重新输入！",2);
		}
    }

	public function ajaxupimg(){
		if(!empty($_FILES['imgfile']['name'])){
			$this->fix = false;
			$this->saveRule = date("YmdHis",time()).rand(0,1000)."_{$this->uid}";
			$this->savePathNew = C('MEMBER_UPLOAD_DIR').'Idcard/' ;
			$this->thumbMaxWidth = "1000,1000";
			$this->thumbMaxHeight = "1000,1000";
			$info = $this->CUpload();
			$img = $info[0]['savepath'].$info[0]['savename'];
		}
		if($img){
			$c = M('member_info')->where("uid = {$this->uid}")->count('uid');
			if($c==1){
				$newid = M("member_info")->where("uid={$this->uid}")->setField('card_img',$img);
			}else{
				$data['uid'] = $this->uid;
				$data['card_img'] = $img;
				$newid = M('member_info')->add($data);
			}
			session("idcardimg","1");
			ajaxmsg('',1);
		}
		else  ajaxmsg('',0);
	}

	public function ajaxupimg2(){
		if(!empty($_FILES['imgfile2']['name'])){
			$this->fix = false;
			$this->saveRule = date("YmdHis",time()).rand(0,1000)."_{$this->uid}_back";
			$this->savePathNew = C('MEMBER_UPLOAD_DIR').'Idcard/' ;
			$this->thumbMaxWidth = "1000,1000";
			$this->thumbMaxHeight = "1000,1000";
			$info = $this->CUpload();
			$img = $info[0]['savepath'].$info[0]['savename'];
		}
		if($img){
			$c = M('member_info')->where("uid = {$this->uid}")->count('uid');
			if($c==1){
				$newid = M("member_info")->where("uid={$this->uid}")->setField('card_back_img',$img);
			}else{
				$data['uid'] = $this->uid;
				$data['card_back_img'] = $img;
				$newid = M('member_info')->add($data);
			}
			session("idcardimg2","1");
			ajaxmsg('',1);
		}
		else  ajaxmsg('',0);
	}

    public function face(){
		$this->assign("face_status",M('members_status')->getFieldById($this->uid,'face_status'));
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

    public function video(){
		$this->assign("video_status",M('members_status')->getFieldById($this->uid,'video_status'));
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
}