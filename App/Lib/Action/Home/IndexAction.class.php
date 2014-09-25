<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends HCommonAction {
    public function index(){
		$per = C('DB_PREFIX');
		
		//资金统计 start
		$map = array();
		$list = M("member_moneylog")->field('type,sum(affect_money) as money')->where($map)->group('type')->select();
		$statData = array();
		$name = C('MONEY_LOG');
		foreach($list as $v) {
			$statData[$v['type']]['money']= ($v['money']>0)?$v['money']:$v['money']*(-1);
			$statData[$v['type']]['name']= $name[$v['type']];
		}
		$this->assign('statData', $statData);
		//资金统计 end

		/*
		//网站公告
		$parm['type_id'] = 321;
		$parm['limit'] = 8;
		$this->assign("noticeList",getArticleList($parm));
		//网站公告
		*/

		/*
		//成功的借款
		$parm=array();
		$searchMap = array();
		$searchMap['b.borrow_status']=array("in",'6,7');
		$parm['map'] = $searchMap;
		$parm['limit'] = 3;
		$parm['orderby']="b.id DESC";
		$successBorrow = getBorrowList($parm);
		$this->assign("successBorrow",$successBorrow);
		//成功的借款
		*/
		
		//正在进行的贷款
		$searchMap = array();
		$searchMap['borrow_status']=array("in",'2,4,6,7');
		$searchMap['is_tuijian']=0;
		$parm=array();
		$parm['map'] = $searchMap;
		$parm['limit'] = 8;
		$parm['orderby']="b.borrow_status ASC,b.id DESC";
		
		$listBorrow = getBorrowList($parm);
		
		$this->assign("listBorrow",$listBorrow);
		//正在进行的贷款

		/*
		if($this->uid){
			$this->assign("m_minfo",M('members')->field('credits')->find($this->uid));
			$this->assign("unread",$read=M("inner_msg")->where("uid={$this->uid} AND status=0")->count('id'));
		}
		*/
		
		/*
		//////////////////////////排行榜//////////////////
		$map = array();
		$start = strtotime(date("Y-m-d",time())." 00:00:00");
		$end = strtotime(date("Y-m-d",time())." 23:59:59");
		$map['add_time'] = array(
						"between",
						"{$start},{$end}"
		);
		$map['status'] = array("in","4,5");
		$listPmday = getranklist($map,10);
		$this->assign("pmDay",$listPmday);
		
		$map = array();
		$start = strtotime("-7 day",strtotime(date("Y-m-d",time())." 00:00:00"));//strtotime(date("Y-m-d",time())." 00:00:00");
		$end = strtotime(date("Y-m-d",time())." 23:59:59");
		$map['add_time'] = array(
						"between",
						"{$start},{$end}"
		);
		$map['status'] = array("in","4,5");
		$listPmweek = getranklist($map,10);
		$this->assign("pmWeek",$listPmweek);
		
		$map = array();
		$xdat = explode("|", $this->glo['rankDate']);
		$start = strtotime($xdat[0]." 00:00:00");
		$end = strtotime($xdat[1]." 23:59:59");
		$map['add_time'] = array(
						"between",
						"{$start},{$end}"
		);
		$map['status'] = array("in","4,5");
		$listPmMonth = getranklist($map,10);
		$this->assign("pmMonth",$listPmMonth);
		
		////////////////////////////////////////////
		*/

		$this->display();
		/****************************募集期内标未满,自动流标 新增 2013-03-13****************************/
		//流标返回
		$mapT = array();
		$mapT['collect_time']=array("lt",time());
		$mapT['borrow_status'] = 2;
		$tlist = M("borrow_info")->field("id,borrow_uid,borrow_type,borrow_money,first_verify_time,borrow_interest_rate,borrow_duration,repayment_type,collect_day,collect_time")->where($mapT)->select();
		if(empty($tlist)) exit;
		foreach($tlist as $key=>$vbx){
			$borrow_id=$vbx['id'];
			//流标
			$done = false;
			$borrowInvestor = D('borrow_investor');
			$binfo = M("borrow_info")->field("borrow_type,borrow_money,borrow_uid,borrow_duration,repayment_type")->find($borrow_id);
			$investorList = $borrowInvestor->field('id,investor_uid,investor_capital')->where("borrow_id={$borrow_id}")->select();
			M('investor_detail')->where("borrow_id={$borrow_id}")->delete();
			if($binfo['borrow_type']==1) $limit_credit = memberLimitLog($binfo['borrow_uid'],12,($binfo['borrow_money']),$info="{$binfo['id']}号标流标");//返回额度
			$borrowInvestor->startTrans();
			
			$bstatus = 3;
			$upborrow_info = M('borrow_info')->where("id={$borrow_id}")->setField("borrow_status",$bstatus);
			//处理借款概要
			$buname = M('members')->getFieldById($binfo['borrow_uid'],'user_name');
			//处理借款概要
			if(is_array($investorList)){
				$upsummary_res = M('borrow_investor')->where("borrow_id={$borrow_id}")->setField("status",$type);
				foreach($investorList as $v){
					MTip('chk15',$v['investor_uid']);//sss
					$accountMoney_investor = M("member_money")->field(true)->find($v['investor_uid']);
					$datamoney_x['uid'] = $v['investor_uid'];
					$datamoney_x['type'] = ($type==3)?16:8;
					$datamoney_x['affect_money'] = $v['investor_capital'];
					$datamoney_x['account_money'] = ($accountMoney_investor['account_money'] + $datamoney_x['affect_money']);//投标不成功返回充值资金池
					$datamoney_x['collect_money'] = $accountMoney_investor['money_collect'];
					$datamoney_x['freeze_money'] = $accountMoney_investor['money_freeze'] - $datamoney_x['affect_money'];
					$datamoney_x['back_money'] = $accountMoney_investor['back_money'];
					
					//会员帐户
					$mmoney_x['money_freeze']=$datamoney_x['freeze_money'];
					$mmoney_x['money_collect']=$datamoney_x['collect_money'];
					$mmoney_x['account_money']=$datamoney_x['account_money'];
					$mmoney_x['back_money']=$datamoney_x['back_money'];
					
					//会员帐户
					$_xstr = ($type==3)?"复审未通过":"募集期内标未满,流标";
					$datamoney_x['info'] = "第{$borrow_id}号标".$_xstr."，返回冻结资金";
					$datamoney_x['add_time'] = time();
					$datamoney_x['add_ip'] = get_client_ip();
					$datamoney_x['target_uid'] = $binfo['borrow_uid'];
					$datamoney_x['target_uname'] = $buname;
					$moneynewid_x = M('member_moneylog')->add($datamoney_x);
					if($moneynewid_x) $bxid = M('member_money')->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
				}
			}else{
				$moneynewid_x = true;
				$bxid=true;
				$upsummary_res=true;
			}
	
			if($moneynewid_x && $upsummary_res && $bxid && $upborrow_info){
				$done=true;
				$borrowInvestor->commit();
			}else{
				$borrowInvestor->rollback() ;
			}
			if(!$done) continue;
			

			MTip('chk11',$vbx['borrow_uid'],$borrow_id);
			$verify_info['borrow_id'] = $borrow_id;
			$verify_info['deal_info_2'] = text($_POST['deal_info_2']);
			$verify_info['deal_user_2'] = 0;
			$verify_info['deal_time_2'] = time();
			$verify_info['deal_status_2'] = 3;
			if($vbx['first_verify_time']>0) M('borrow_verify')->save($verify_info);
			else  M('borrow_verify')->add($verify_info);
			
			$vss = M("members")->field("user_phone,user_name")->where("id = {$vbx['borrow_uid']}")->find();
			SMStip("refuse",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$verify_info['borrow_id']));
			//@SMStip("refuse",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$verify_info['borrow_id']));
			//updateBinfo
			$newBinfo=array();
			$newBinfo['id'] = $borrow_id;
			$newBinfo['borrow_status'] = 3;
			$newBinfo['second_verify_time'] = time();
			$x = M("borrow_info")->save($newBinfo);
		}
		/****************************募集期内标未满,自动流标 新增 2013-03-13****************************/
	}
}