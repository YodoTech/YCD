<?php
// 本类由系统自动生成，仅供测试用途
class ServerAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function upleve(){
		$vo = M('members')->field('user_leve,account_money')->find($this->uid);
		$this->assign("vo",$vo);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	public function actupleve(){
		$money = intval($_POST['upmoney']);
		$vo = M('members')->field('user_leve,account_money,time_limit,recommend_id')->find($this->uid);
		$levemoney = $vo['account_money'] - $money;
		if($levemoney<0) ajaxmsg("帐户余额不足",0);
		($vo['time_limit']>time())?$oldtime = $vo['time_limit']:$oldtime = time();
		
		if($money==25) $time_limit = strtotime(date("Y-m-d",strtotime("+1 months",$oldtime))." 23:59:59");
		elseif($money==100) $time_limit = strtotime(date("Y-m-d",strtotime("+6 months",$oldtime))." 23:59:59");
		elseif($money==150) $time_limit = strtotime(date("Y-m-d",strtotime("+12 months",$oldtime))." 23:59:59");
		$res = memberMoneyLog($this->uid,2,$money,"会员特权延长至".date("Y-m-d",$time_limit)."到期");
		if($res){
			$xmoney = M('members')->getFieldById($vo['recommend_id'],'reward_money');
			memberMoneyLog($vo['recommend_id'],13,5,session('u_user_name')."升级特权会员奖励");
			$sdata['id'] = $this->uid;
			$sdata['time_limit'] = $time_limit;
			$sdata['user_leve'] = 1;
			$newid = M('members')->save($sdata);
			if($newid) ajaxmsg();
			else ajaxmsg("升级出错，请重试",0);
		}else{
			ajaxmsg("帐户余额处理出错，请重试",0);
		}	
	
	}
	
    public function uplevelog(){
		$map['uid'] = $this->uid;
		$map['type'] = 2;
		$list = getMoneyLog($map,100);
		$this->assign("list",$list['list']);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

}