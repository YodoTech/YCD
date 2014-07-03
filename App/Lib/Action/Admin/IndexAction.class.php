<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends ACommonAction {

	var $justlogin = true;
	
    public function index(){
		require(C('APP_ROOT')."Common/acl.inc.php");
		require(C('APP_ROOT')."Common/menu.inc.php");
		
       	$this->assign('menu_left',$menu_left);
		$this->display();
    }
	public function verify(){
		import("ORG.Util.Image");
		Image::buildImageVerify();
	}

    public function login()
    {
		require C("APP_ROOT")."Common/menu.inc.php";
		if( session("admin") > 0){
			$this->redirect('index');
			exit;
		}
		if($_POST){
			if($_SESSION['verify'] != md5($_POST['code'])){
				$this->error("验证码错误!");
			}
			$data['user_name'] = text($_POST['admin_name']);
			$data['user_pass'] = md5(strtolower($_POST['admin_pass']));
			$data['is_ban'] = array('neq','1');
			$data['user_word'] = text($_POST['user_word']);
			$admin = M('ausers')->field('id,user_name,u_group_id,real_name,is_kf,area_id,user_word')->where($data)->find();
			
			if(is_array($admin) && count($admin)>0 ){
				foreach($admin as $key=>$v){
					session("admin_{$key}",$v);
				}
				if(session("admin_area_id")==0) session("admin_area_id","-1");
				session('admin',$admin['id']);
				session('adminname',$admin['real_name']);
				$this->assign('jumpUrl', "__URL__/index");
				$this->success('登陆成功，现在转向管理主页');
			}else{
				$this->error('用户名或密码或口令错误，登陆失败');
			}
		}else{
			$this->display();
		}
    }


    public function logout()
    {
		require C("APP_ROOT")."Common/menu.inc.php";
		session(null);
		$this->assign('jumpUrl', '/');
		$this->success('注销成功，现在转向首页');
    }
	
}