<?php
// 全局设置
class MemberidAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$pre = C('DB_PREFIX');
		$field= true;
		$map=array();
		
		
		if($_REQUEST['status']){
			$map['s.id_status'] = intval($_REQUEST['status']);
			$search['status'] = $map['s.id_status'];
		}
		else  $map['s.id_status'] = array("in","1,3");
		
		
		if($_REQUEST['uname']){
			$map['m.user_name'] = text($_REQUEST['uname']);
			$search['uname'] = $map['m.user_name'];
		}

		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['real_name'] = $map['mi.real_name'];	
		}

		if($_REQUEST['idcard']){
			$map['mi.idcard'] = urldecode($_REQUEST['idcard']);
			$search['idcard'] = $map['mi.idcard'];	
		}
		
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['mi.up_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['mi.up_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['mi.up_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		

		import("ORG.Util.Page");
		$count = M('members_status s')->join("{$pre}members m ON m.id=s.uid")->join("{$pre}member_info mi ON mi.uid=s.uid")->where($map)->count('s.uid');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		$list = M('members_status s')->field('s.uid,s.id_status,mi.up_time,mi.card_img,mi.card_back_img,mi.real_name,mi.idcard,m.user_name')->join("{$pre}members m ON m.id=s.uid")->join("{$pre}member_info mi ON mi.uid=s.uid")->where($map)->order("mi.up_time DESC")->limit($Lsql)->select();

        $this->assign("status", array("1"=>'已认证',"3"=>'待认证'));
		$this->assign("search",$search);
		$this->assign("list",$list);
		$this->assign("pagebar",$page);
        $this->display();
    }
    public function edit() {
		setBackUrl();
        $id = intval($_REQUEST['id']);
        $this->assign('uid', $id);
        $this->display();
    }
	
	public function doEdit(){
		$status = intval($_POST['status']);
		$uid = intval($_POST['id']);
		$credits = intval($_POST['deal_credits']);
		if($status==1){
			if($credits>0) memberCreditsLog($uid,2,$credits,"实名认证通过");
			memberMoneyLog($uid,25,-($this->glo['fee_idcard']),$info="实名认证通过");
			$newxid = M("members_status")->where("uid={$uid}")->setField('id_status',1);
			
			$data['status']=1;
			$data['deal_info']=$deal_info;
			$new= M("name_apply")->where("uid={$uid}")->save($data);
		}else{
			$newxid = M("members_status")->where("uid={$uid}")->setField('id_status',0);
			
			$data['deal_info']=$deal_info;
			$new= M("name_apply")->where("uid={$uid}")->save($data);
		}
		if($newxid) $this->success("审核成功",__URL__."/index".session('listaction'));
		else $this->error("审核失败");
	}

}
?>