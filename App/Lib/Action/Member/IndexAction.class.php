<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends MCommonAction {
    public function index(){
		$ucLoing = de_xie($_COOKIE['LoginCookie']);
		setcookie('LoginCookie','',time()-10*60,"/");
		$this->assign("uclogin",$ucLoing);
		
		$this->assign("unread",$read=M("inner_msg")->where("uid={$this->uid} AND status=0")->count('id'));
		$minfo =getMinfo($this->uid,true);
		$this->assign("minfo",$minfo);
		$this->assign("MinfoDone",getMemberInfoDone($this->uid));
		$this->assign("mstatus", M('members_status')->field(true)->find($this->uid));
		$this->assign("capitalinfo", getMemberBorrowScan($this->uid));
		$this->assign("memberinfo", M('members')->find($this->uid));
		$this->assign("memberdetail", M('member_info')->find($this->uid));
		$toubiaojl =M('borrow_investor')->where("borrow_uid={$this->uid}")->sum('reward_money');//支付投标奖励
		$this->assign("toubiaojl", $toubiaojl);//支付投标奖励
		
		//////////////////////////////////
		$moneylog = M("member_moneylog")->field("type,sum(affect_money) as money")->where("uid={$this->uid}")->group("type")->select();
		$row1=array();
		foreach($moneylog as $vs){
			$row1[$vs['type']]['money']= ($vs['money']>0)?$vs['money']:$vs['money']*(-1);
		}
		
		$map=array();
		$map['withdraw_status'] =2;
		$tx = M('member_withdraw')->field("uid,sum(withdraw_money) as withdraw_money,sum(withdraw_fee) as withdraw_fee")->where("uid={$this->uid} and withdraw_status=2")->group("uid")->select();
		foreach($tx as $vt){
			$row1['tx']['withdraw_money']= $vt['withdraw_money'];	//成功提现金额	
			$row1['tx']['withdraw_fee']= $vt['withdraw_fee'];	//提现手续费
		}
		
		$this->assign("list",$row1);
		$this->assign("bank",M('member_banks')->field('bank_num')->find($this->uid));
		$czfee = M('member_payonline')->where("uid={$uid} AND status=1")->sum('fee');//在线充值手续费总金额
		
		$capitalinfo = getMemberBorrowScan($this->uid);
		$intotal = $capitalinfo['tj']['earnInterest']+$row1['20']['money']+$row1['34']['money']+$row1['13']['money']+$row1['32']['money'];//收入总和
		//$outtotal = $capitalinfo['tj']['payInterest']+$toubiaojl+$row1['tx']['withdraw_money']+$row1['14']['money']+$row1['22']['money']+$row1['25']['money']+$row1['26']['money']+$row1['18']['money']+$row1['30']['money']+$row1['31']['money']-$czfee;//支出总和
		$outtotal = $capitalinfo['tj']['payInterest']+$toubiaojl+$row1['tx']['withdraw_fee']+$row1['14']['money']+$row1['22']['money']+$row1['25']['money']+$row1['26']['money']+$row1['18']['money']+$row1['30']['money']+$row1['31']['money']+$czfee;//支出总和

		$this->assign("intotal",$intotal);
		$this->assign("outtotal",$outtotal);
		/////////////////////////////////
		
		//理财
		$investList = getTenderList(array('investor_uid'=>$this->uid,'status'=>4),0,5);
		$investList['count'] = count($investList['list']);
		$this->assign("investList", $investList);
		//借款
		$borrowList = getBorrowList(array('borrow_uid'=>$this->uid,'borrow_status'=>6,'status'=>7),0,5);
		$borrowList['count'] = count($borrowList['list']);
		$this->assign("borrowList", $borrowList);
		

		$this->assign("kflist",get_admin_name());
		$list=array();
		$pre = C('DB_PREFIX');
		$rule = M('ausers u')->field('u.id,u.qq,u.phone')->join("{$pre}members m ON m.customer_id=u.id")->where("u.is_kf =1 and m.customer_id={$minfo['customer_id']}")->select();
		foreach($rule as $key=>$v){
			$list[$key]['qq']=$v['qq'];
			$list[$key]['phone']=$v['phone'];
		}
		$this->assign("kfs",$list);
		
		//认证信息
		$verifyStatus = m("members m")->field("m.id,m.user_leve,m.time_limit,s.id_status,s.phone_status,s.email_status,s.video_status,s.face_status")->join("{$pre}members_status s ON s.uid=m.id")->where("m.id={$this->uid}")->find();
	    $this->assign('verifyStatus', $verifyStatus);

	    //推荐的贷款
		$searchMap = array();
		$searchMap['borrow_status']=array("in",'2,4,6,7');
		$searchMap['is_tuijian']=1;
		//$searchMap['collect_time']=array('gt',time());
		$parm=array();
		$parm['map'] = $searchMap;
		$parm['limit'] = 3;
		$parm['orderby']="b.id DESC";
		$listBorrowtj = getBorrowList($parm);
		$this->assign("listBorrow_tj",$listBorrowtj);
		//推荐的贷款
		
		$this->display();
    }
}