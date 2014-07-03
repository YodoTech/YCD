<?php
// 本类由系统自动生成，仅供测试用途
class MoneylimitAction extends MCommonAction {

    public function index(){
		$this->display();
    }
	public function apply(){
		$xtime = strtotime("-1 month");
		$vo = M('member_apply')->field('apply_status')->where("uid={$this->uid}")->order("id DESC")->find();
		$xcount = M('member_apply')->field('add_time')->where("uid={$this->uid} AND add_time>{$xtime}")->order("id DESC")->find();
		if(is_array($vo) && $vo['apply_status']==0){
			$xs = "是您的申请正在审核，请等待此次审核结束再提交新的申请";
			ajaxmsg($xs,0);
		}elseif(is_array($xcount)){
			$timex = date("Y-m-d",$xcount['add_time']);
			$xs = "一个月内只能进行一次额度申请，您已在{$timex}申请过了，如急需额度，请直接联系客服";
			ajaxmsg($xs,0);
		}else{
			$apply['uid'] = $this->uid; 
			$apply['apply_type'] = intval($_POST['apply_type']); 
			$apply['apply_money'] = floatval($_POST['apply_money']); 
			$apply['apply_info'] = text($_POST['apply_info']); 
			$apply['add_time'] = time(); 
			$apply['apply_status'] = 0; 
			$apply['add_ip'] = get_client_ip(); 
			$nid = M('member_apply')->add($apply);
		}		
		if($nid) ajaxmsg('申请已提交，请等待审核');
		else ajaxmsg('申请提交失败，请重试',0);
	}


	public function applylog(){
		$Binfo = require C("APP_ROOT")."Conf/borrow_config.php";
		
		$size=10;
		$map['uid'] = $this->uid;
	
		//分页处理
		import("ORG.Util.Page");
		$count = M('member_apply')->where($map)->count('id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$status_arr =array('待审核','审核通过','审核未通过');
		$list = M('member_apply')->where($map)->order('id DESC')->limit($Lsql)->select();
		foreach($list as $key=>$v){
			$list[$key]['status'] = $status_arr[$v['apply_status']];
		}

		$this->assign("aType",$Binfo['APPLY_TYPE']);
		$this->assign("list",$list);
		$this->assign("pagebar",$page);

		$json['html'] = $this->fetch();
		exit(json_encode($json));
	}

}