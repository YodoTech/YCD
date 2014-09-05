<?php
// 本类由系统自动生成，仅供测试用途
class CommonAction extends MCommonAction {
	var $notneedlogin=true;
    public function index(){
		$this->display();
    }
	
    public function login() {
    	$redirect_uri = urlencode($_GET['redirect_uri']);
    	$this->assign('redirect_uri', $redirect_uri);
    	
		$this->display();
    }
	
    public function register() {
		if($_GET['invite']){
			//$uidx = M('members')->getFieldByUserName(text($_GET['invite']),'id');
			//if($uidx>0) session("tmp_invite_user",$uidx);
			session("tmp_invite_user",$_GET['invite']);
		}

    	//激活验证
    	if ($this->verifyStatus === false) {
    		$userEmail = M('members')->getFieldById($this->uid,'user_email');
    		$this->assign('userEmail', $userEmail);
    		$this->assign('message','我们给您的邮箱<span style="color:#930303; font-weight:bold; margin:0 3px;">'.$userEmail.'</span>发送了确认邮件，请点击邮件中的链接进行确认。');
    		$this->display('regcheck');
    	} elseif($this->verifyStatus === true) {
    		//...
    	} else {
    		$this->display();
    	}
    }
	
	private function actlogin_bak() {
		(false!==strpos($_POST['sUserName'],"@"))?$data['user_email'] = text($_POST['sUserName']):$data['user_name'] = text($_POST['sUserName']);
		$vo = M('members')->field('id,user_name,user_email,user_pass')->where($data)->find();
		if($vo){
			$this->_memberlogin($vo['id']);
			ajaxmsg();
		}else{
			ajaxmsg("用户名不存在",0);	
		}
	}
	
	public function actlogin(){
		setcookie('LoginCookie','',time()-10*60,"/");
		//uc登陆
		
		$loginconfig = FS("Webconfig/loginconfig");
		$uc_mcfg  = $loginconfig['uc'];
		if($uc_mcfg['enable']==1){
			require_once C('APP_ROOT')."Lib/Uc/config.inc.php";
			require C('APP_ROOT')."Lib/Uc/uc_client/client.php";
		}

		//uc登陆
		if($_SESSION['verify'] != md5(strtolower($_POST['sVerCode']))) ajaxmsg("验证码错误!",0);
		
		
		(false!==strpos($_POST['sUserName'],"@"))?$data['user_email'] = text($_POST['sUserName']):$data['user_name'] = text($_POST['sUserName']);
		$vo = M('members')->field('id,user_name,user_email,user_pass,is_ban')->where($data)->find();
		if($vo['is_ban']==1) ajaxmsg("您的帐户已被冻结，请联系客服处理！",0);
		if(!is_array($vo)){
			//本站登陆不成功，偿试uc登陆及注册本站
			if($uc_mcfg['enable']==1){
				list($uid, $username, $password, $email) = uc_user_login(text($_POST['sUserName']), text($_POST['sPassword']));
				if($uid > 0) {
					$regdata['txtUser'] = text($_POST['sUserName']);
					$regdata['txtPwd'] = text($_POST['sPassword']);
					$regdata['txtEmail'] = $email;
					$newuid = $this->ucreguser($regdata);
					if(is_numeric($newuid)&&$newuid>0){
						$logincookie = uc_user_synlogin($uid);//UC同步登陆
						setcookie('LoginCookie',$logincookie,time()+10*60,"/");
						$this->_memberlogin($newuid);
						ajaxmsg();//登陆成功
					}else{
						ajaxmsg($newuid,0);
					}
				}
			}
			//本站登陆不成功，偿试uc登陆及注册本站
			ajaxmsg("用户名或者密码错误！",0);
		}else{
			if($vo['user_pass'] == md5($_POST['sPassword']) ){//本站登陆成功，uc登陆及注册UC
				//uc登陆及注册UC
				if($uc_mcfg['enable']==1){
					$dataUC = uc_get_user($vo['user_name']);
					if($dataUC[0] > 0) {
						$logincookie = uc_user_synlogin($dataUC[0]);//UC同步登陆
						setcookie('LoginCookie',$logincookie,time()+10*60,"/");
					}else{
						$uid = uc_user_register($vo['user_name'], $_POST['sPassword'], $vo['user_email']);
						if($uid>0){
							$logincookie = uc_user_synlogin($dataUC[0]);//UC同步登陆
							setcookie('LoginCookie',$logincookie,time()+10*60,"/");
						}
					}
				}
				//uc登陆及注册UC
				$this->_memberlogin($vo['id']);
				ajaxmsg();
			}else{//本站登陆不成功
				ajaxmsg("用户名或者密码错误！",0);
			}
		}
	}
	
	public function actlogout(){
		$this->_memberloginout();
		//uc登陆
		$loginconfig = FS("Webconfig/loginconfig");
		$uc_mcfg  = $loginconfig['uc'];
		if($uc_mcfg['enable']==1){
			require_once C('APP_ROOT')."Lib/Uc/config.inc.php";
			require C('APP_ROOT')."Lib/Uc/uc_client/client.php";
			$logout = uc_user_synlogout();
		}
		//uc登陆
		$this->assign("uclogout",de_xie($logout));
		$this->success("注销成功",__APP__."/");
	}
	
	private function ucreguser($reg){
		$data['user_name'] = text($reg['txtUser']);
		$data['user_pass'] = md5($reg['txtPwd']);
		$data['user_email'] = text($reg['txtEmail']);
		$count = M('members')->where("user_email = '{$data['user_email']}' OR user_name='{$data['user_name']}'")->count('id');
		if($count>0) return "登陆失败,UC用户名冲突,用户名或者邮件已经有人使用";
		$data['reg_time'] = time();
		$data['reg_ip'] = get_client_ip();
		$data['lastlog_time'] = time();
		$data['lastlog_ip'] = get_client_ip();
		$newid = M('members')->add($data);
		
		if($newid){
			session('u_id',$newid);
			session('u_user_name',$data['user_name']);
			Notice(1,$newid,array('email',$data['user_email']));
			//memberMoneyLog($newid,1,$this->glo['award_reg'],"注册奖励");
			return $newid;
		}
		return "登陆失败,UC用户名冲突";
	}
	
	
	public function regaction(){
		if ($this->verifyStatus === false) {
			$data['user_email'] = text($_POST['txtEmail']);
			if (Notice(1, $this->uid, array('email',$data['user_email']))) {
				ajaxmsg('发送成功');
			} else {
				ajaxmsg('发送失败，请重试', 0);
			}
		} elseif ($this->verifyStatus === true) {
			ajaxmsg('发送失败，邮箱已验证', 0);
		} else {
			$data['user_name'] = text($_POST['txtUser']);
			$data['user_pass'] = md5($_POST['txtPwd']);
			$data['user_email'] = text($_POST['txtEmail']);
			$data['user_role'] = MY_FUNC::intval($_POST['txtRole'], array(1, 2));
			//数据校验
			foreach ($data as $v) {
				if (empty($v)) ajaxmsg('请按要求填写完整', 0);
			}

			$count = M('members')->where("user_email = '{$data['user_email']}' OR user_name='{$data['user_name']}'")->count('id');
			if($count>0) ajaxmsg("注册失败，用户名或者邮件已经有人使用",0);
			//uc注册
			$loginconfig = FS("Webconfig/loginconfig");
			$uc_mcfg  = $loginconfig['uc'];
			if($uc_mcfg['enable']==1){
				require_once C('APP_ROOT')."Lib/Uc/config.inc.php";
				require C('APP_ROOT')."Lib/Uc/uc_client/client.php";
				$uid = uc_user_register($data['user_name'], $_POST['txtPwd'], $data['user_email']);
				if($uid <= 0) {
					if($uid == -1) {
						ajaxmsg('用户名不合法',0);
					} elseif($uid == -2) {
						ajaxmsg('包含要允许注册的词语',0);
					} elseif($uid == -3) {
						ajaxmsg('用户名已经存在',0);
					} elseif($uid == -4) {
						ajaxmsg('Email 格式有误',0);
					} elseif($uid == -5) {
						ajaxmsg('Email 不允许注册',0);
					} elseif($uid == -6) {
						ajaxmsg('该 Email 已经被注册',0);
					} else {
						ajaxmsg('未定义',0);
					}
				}
			}
			//uc注册
			
			$data['reg_time'] = time();
			$data['reg_ip'] = get_client_ip();
			$data['lastlog_time'] = time();
			$data['lastlog_ip'] = get_client_ip();
			if(session("tmp_invite_user"))  $data['recommend_id'] = session("tmp_invite_user");
			$newid = M('members')->add($data);
			
			if($newid){
				session('u_id',$newid);
				session('u_user_name',$data['user_name']);
				//memberMoneyLog($newid,1,$this->glo['award_reg'],"注册奖励");
				if (Notice(1, $newid, array('email',$data['user_email']))) {
					ajaxmsg();
				} else {
					ajaxmsg('请手动发送激活邮件');
				}
			}
			else  ajaxmsg('注册失败，请重试',0);
		}
	}
	
	public function emailverify(){
		$code = text($_GET['vcode']);
		$uk = is_verify(0,$code,1,60*1000);
		if(false===$uk){
			$this->error("验证失败");
		}else{
			$this->assign("waitSecond",3);
			$count = M('members_status')->where("uid={$uk}")->count('uid');
			$data['email_status']=1;
			if($count==0){
				$data['uid']=$uk;
				$data['email_status']=1;
				M('members_status')->where("uid={$uk}")->add($data);
			}else{
				M('members_status')->where("uid={$uk}")->save($data);
			}
			$this->success("验证成功",__APP__."/member");
		}
	}
	
	public function getpasswordverify(){
		$code = text($_GET['vcode']);
		$uk = is_verify(0,$code,7,60*1000);
		if(false===$uk){
			$this->error("验证失败");
		}else{
			session("temp_get_pass_uid",$uk);
			$this->display('getpass');
		}
	}
	
	public function setnewpass(){
		$d['content'] = $this->fetch();
		echo json_encode($d);
	}
	
	public function dosetnewpass(){
		$per = C('DB_PREFIX');
		$uid = session("temp_get_pass_uid");
		$oldpass = M("members")->getFieldById($uid,'user_pass');
		if($oldpass == md5($_POST['pass'])){
			$newid = true;
		}else{
			$newid = M()->execute("update {$per}members set `user_pass`='".md5($_POST['pass'])."' where id={$uid}");
		}
		
		if($newid){
			session("temp_get_pass_uid",NULL);
			ajaxmsg();
		}else{
			ajaxmsg('',0);
		}
	}
	
	
	public function ckuser(){
		$map['user_name'] = text($_POST['UserName']);
		$count = M('members')->where($map)->count('id');
        
		if ($count>0) {
			$json['status'] = 0;
			exit(json_encode($json));
        } else {
			$json['status'] = 1;
			exit(json_encode($json));
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
	
	public function ckcode(){
		if($_SESSION['verify'] != md5(strtolower($_POST['sVerCode']))) {
			echo (0);
		 }else{
			echo (1);
        }
	}
	
	public function verify(){
		import("ORG.Util.Image");
		$backArr = array(255,255,255);//背景色
		$borderArr = array(255,255,255);//边框色
		$stringArr = array(250,111,21);//字体颜色
		Image::buildImageVerify2(4, 5, 'png', 104, 46, 'verify', $backArr, $borderArr, $stringArr);
	}
	
	public function regsuccess() {
		$code = text($_GET['vcode']);
		$uk = is_verify(0,$code,1,60*1000);
		if(false === $uk) {
			//根据当前用户进行判断
			if ($this->verifyStatus === true) {//已验证
				//...
			} elseif ($this->verifyStatus === false) {//待验证
				$this->assign('userEmail', M('members')->getFieldById($this->uid, 'user_email'));
	    		$this->assign('message','<span class="red">验证失败</span>，请<a href="#" class="reSend">重发</a>邮件。');
				$this->display('regcheck');
			} else {//未登录
				redirect(__APP__.'/member/login/');
			}
		} else {
			$count = M('members_status')->where("uid={$uk}")->count('uid');
			$data['email_status']=1;
			if($count == 0) {
				$data['uid'] = $uk;
				$data['email_status'] = 1;
				M('members_status')->where("uid={$uk}")->add($data);
			} else {
				M('members_status')->where("uid={$uk}")->save($data);
			}
			$this->display();
		}
	}

	public function getpassword(){
		$d['content'] = $this->fetch();
		echo json_encode($d);
	}

	public function dogetpass(){
		(false!==strpos($_POST['u'],"@"))?$data['user_email'] = text($_POST['u']):$data['user_name'] = text($_POST['u']);
		$vo = M('members')->field('id')->where($data)->find();
		if(is_array($vo)){
			$res = Notice(7,$vo['id']);
			if($res) ajaxmsg();
			else ajaxmsg('',0);
		}else{
			ajaxmsg('',0);
		}
	}

}