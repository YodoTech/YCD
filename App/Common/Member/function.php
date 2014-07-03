<?php
function getFriendList($map,$size,$xuid=0){
	//if(empty($map['f.uid'])) return;
	$pre = C('DB_PREFIX');
	
	//分页处理
	import("ORG.Util.Page");
	$count = M('member_friend f')->where($map)->count('f.id');
	$p = new Page($count, $size);
	$page = $p->show();
	$Lsql = "{$p->firstRow},{$p->listRows}";
	//分页处理

	$list = M('member_friend f')->field("f.uid,f.friend_id,f.add_time,m.user_name,m.credits,fm.user_name as funame,fm.credits as fcredits")->join("{$pre}members m ON f.uid = m.id")->join("{$pre}members fm ON f.friend_id = fm.id")->where($map)->limit($Lsql)->select();
	foreach($list as $key=>$v){
		if($map['f.apply_status']==0){
			$list[$key]['user_name'] = $v['user_name'];
			$list[$key]['credits'] = $v['credits'];
		}else{
			$list[$key]['user_name'] = $v['funame'];
			$list[$key]['credits'] = $v['fcredits'];
		}
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	return $row;
}
//获取商品,包括分页数据
function getMsgList($parm=array()){
	$M = new Model('member_msg');
	$pre = C('DB_PREFIX');
	$field=true;
	$orderby = " id DESC";
	
	
	if($parm['pagesize']){
		//分页处理
		import("ORG.Util.Page");
		$count = $M->where($parm['map'])->count('id');
		$p = new Page($count, $parm['pagesize']);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}

	$data = M('member_msg')->field(true)->where($parm['map'])->order($orderby)->limit($Lsql)->select();
		
	$symbol = C('MONEY_SYMBOL');
	$suffix=C("URL_HTML_SUFFIX");
	foreach($data as $key=>$v){
/*		if($v['bids_type']==1){
			$data[$key]['bids_money'] = 0;
			$data[$key]['bids_free'] = $v['bids'];
		}else{
			$data[$key]['bids_money'] = $v['bids'];
			$data[$key]['bids_free'] = 0;
		}
*/	}
	
	$row=array();
	$row['list'] = $data;
	$row['page'] = $page;
	$row['count'] = $count;
	return $row;

}

function getWithDrawLog($map,$size,$limit=10){
	if(empty($map['uid'])) return;
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('member_withdraw')->where($map)->count('id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$status_arr =array('待审核','审核通过,处理中','已提现','审核未通过');
	$list = M('member_withdraw')->where($map)->order('id DESC')->limit($Lsql)->select();
	foreach($list as $key=>$v){
		$list[$key]['status'] = $status_arr[$v['withdraw_status']];
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	$map['status'] = 1;
	$row['success_money'] = M('member_payonline')->where($map)->sum('money');
	$map['status'] = array('neq','1');
	$row['fail_money'] = M('member_payonline')->where($map)->sum('money');
	return $row;
}

function getChargeLog($map,$size,$limit=10){
	if(empty($map['uid'])) return;
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('member_payonline')->where($map)->count('id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$status_arr =array('充值未完成','充值成功','签名不符','充值失败');
	$list = M('member_payonline')->where($map)->order('id DESC')->limit($Lsql)->select();
	foreach($list as $key=>$v){
		$list[$key]['status'] = $status_arr[$v['status']];
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	$map['status'] = 1;
	$row['success_money'] = M('member_payonline')->where($map)->sum('money');
	$map['status'] = array('neq','1');
	$row['fail_money'] = M('member_payonline')->where($map)->sum('money');
	return $row;
}
//集合起每笔借款的每期的还款状态(逾期)
function getMBreakRepaymentList($uid=0,$size=10,$Wsql=""){
	if(empty($uid)) return;
	$pre = C('DB_PREFIX');
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M()->query("select d.id as count from {$pre}investor_detail d where d.borrow_id in(select tb.id from {$pre}borrow_info tb where tb.borrow_uid={$uid}) AND tb.borrow_status=6 AND d.deadline<".time()." AND d.repayment_time=0 {$Wsql} group by d.sort_order,d.borrow_id");
		$count = count($count);
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$field = "b.borrow_name,d.status,d.total,d.borrow_id,d.sort_order,sum(d.capital) as capital,sum(d.interest) as interest,d.deadline";
	$sql = "select {$field} from {$pre}investor_detail d left join {$pre}borrow_info b ON b.id=d.borrow_id where d.borrow_uid ={$uid} AND b.borrow_status=6 AND d.deadline<".time()." AND d.repayment_time=0 {$Wsql} group by d.sort_order,d.borrow_id order by  d.borrow_id,d.sort_order limit {$Lsql}";

	$list = M()->query($sql);
	$status_arr =array('还未还','已还完','已提前还款','愈期还款','网站代还本金');
	$glodata = get_global_setting();
	$expired = explode("|",$glodata['fee_expired']);
	$call_fee = explode("|",$glodata['fee_call']);
	foreach($list as $key=>$v){
		$list[$key]['status'] = $status_arr[$v['status']];
		$list[$key]['breakday'] = getExpiredDays($v['deadline']);
		
		if($list[$key]['breakday']>$expired[0]){
			$list[$key]['expired_money'] = getExpiredMoney($list[$key]['breakday'],$v['capital'],$v['interest']);
		}
		
		if($list[$key]['breakday']>$call_fee[0]){
			$list[$key]['call_fee'] = getExpiredCallFee($list[$key]['breakday'],$v['capital'],$v['interest']);
		}
		
		$list[$key]['allneed'] = $list[$key]['call_fee'] + $list[$key]['expired_money'] + $v['capital'] + $v['interest'];
	}
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	$row['count'] = $count;
	return $row;
}


//集合起每笔借款的每期的还款状态(逾期)
function getMBreakInvestList($map,$size=10){
	$pre = C('DB_PREFIX');
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('investor_detail d')->where($map)->count('d.id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$field = "m.user_name as borrow_user,b.borrow_interest_rate,d.borrow_id,b.borrow_name,d.status,d.total,d.borrow_id,d.sort_order,d.interest,d.capital,d.deadline,d.sort_order";
	$list =M('investor_detail d')->field($field)->join("{$pre}borrow_info b ON b.id=d.borrow_id")->join("{$pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->select();

	$status_arr =array('还未还','已还完','已提前还款','愈期还款','网站代还本金');
	$glodata = get_global_setting();
	$expired = explode("|",$glodata['fee_expired']);
	$call_fee = explode("|",$glodata['fee_call']);
	foreach($list as $key=>$v){
		$list[$key]['status'] = $status_arr[$v['status']];
		$list[$key]['breakday'] = getExpiredDays($v['deadline']);
	}
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	$row['count'] = $count;
	return $row;
}

function getBorrowList($map,$size,$limit=10){
	if(empty($map['borrow_uid'])) return;
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info')->where($map)->count('id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
	$status_arr =$Bconfig['BORROW_STATUS_SHOW'];
	$type_arr =$Bconfig['REPAYMENT_TYPE'];
	//$list = M('borrow_info')->where($map)->order('id DESC')->limit($Lsql)->select();
	/////////////使用了视图查询操作 fans 2013-05-22/////////////////////////////////
	$Model = D("BorrowView");
	$list=$Model->field(true)->where($map)->order('times ASC')->group('id')->limit($Lsql)->select();

	/////////////使用了视图查询操作 fans 2013-05-22/////////////////////////////////
	foreach($list as $key=>$v){
		$list[$key]['status'] = $status_arr[$v['borrow_status']];
		$list[$key]['repayment_type_num'] = $v['repayment_type'];
		$list[$key]['repayment_type'] = $type_arr[$v['repayment_type']];
		$list[$key]['progress'] = getFloatValue($v['has_borrow']/$v['borrow_money']*100,2);
		if($map['borrow_status']==6){
			$vx = M('investor_detail')->field('deadline')->where("borrow_id={$v['id']} and status=7")->order("deadline ASC")->find();
			$list[$key]['repayment_time'] = $vx['deadline'];
		}
		if($map['borrow_status']==5 || $map['borrow_status']==1){
			$vd = M('borrow_verify')->field(true)->where("borrow_id={$v['id']}")->find();
			$list[$key]['dealinfo'] = $vd;
		}
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	//$map['status'] = 1;
	//$row['success_money'] = M('member_payonline')->where($map)->sum('money');
	//$map['status'] = array('neq','1');
	//$row['fail_money'] = M('member_payonline')->where($map)->sum('money');
	return $row;
}


function getTenderList($map,$size,$limit=10){
	$pre = C('DB_PREFIX');
	$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
	//if(empty($map['i.investor_uid'])) return;
	if(empty($map['investor_uid'])) return;
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_investor i')->where($map)->count('i.id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$type_arr =C('BORROW_TYPE');
	/*$field = "i.*,i.add_time as invest_time,m.user_name as borrow_user,b.borrow_duration,b.has_pay,b.borrow_interest_rate,b.add_time as borrow_time,b.borrow_money,b.borrow_name,m.credits,b.repayment_type,b.borrow_type";
	$list = M('borrow_investor i')->field($field)->where($map)->join("{$pre}borrow_info b ON b.id=i.borrow_id")->join("{$pre}members m ON m.id=b.borrow_uid")->order('i.deadline ASC')->limit($Lsql)->select();*/
	
	
	/////////////////////////视图查询 fan 20130522//////////////////////////////////////////
	$Model = D("TenderListView");
	$list=$Model->field(true)->where($map)->order('times ASC')->group('id')->limit($Lsql)->select();
	////////////////////////视图查询 fan 20130522//////////////////////////////////////////
	foreach($list as $key=>$v){
		//if($map['i.status']==4){
		if($map['status']==4){
			$list[$key]['total'] = ($v['borrow_type']==3)?"1":$v['borrow_duration'];
			$list[$key]['back'] = $v['has_pay'];
			$vx = M('investor_detail')->field('deadline')->where("borrow_id={$v['borrowid']} and status=7")->order("deadline ASC")->find();
			$list[$key]['repayment_time'] = $vx['deadline'];
		}
	}

	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	$row['total_money'] = M('borrow_investor i')->where($map)->sum('investor_capital');
	$row['total_num'] = $count;
	return $row;
}


function getBackingList($map,$size,$limit=10){
	$pre = C('DB_PREFIX');
	if(empty($map['d.investor_uid'])) return;
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('investor_detail d')->where($map)->count('d.id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="{$parm['limit']}";
	}
	
	$type_arr =C('BORROW_TYPE');
	$field = true;
	$list = M('investor_detail d')->field($field)->where($map)->order('d.id DESC')->limit($Lsql)->select();
	foreach($list as $key=>$v){
		//$list[$key]['status'] = $status_arr[$v['status']];
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	$sx = M('investor_detail d')->field("sum(d.capital + d.interest) as tox")->where("d.status=1 AND d.investor_uid={$map['d.investor_uid']}")->find();
	$sxcount = M('borrow_investor')->where("status=4 AND investor_uid={$map['d.investor_uid']}")->count("id");
	$month = M('investor_detail d')->field("sum(d.capital + d.interest) as tox")->where($map)->find();
	$row['month_total'] = $month['tox'];
	$row['total_money'] = $sx['tox'];
	$row['total_num'] = $sxcount;
	return $row;
}


function getMVouchList($map,$size){
	$pre = C('DB_PREFIX');
	if(empty($map['v.uid'])) return;
	
	if($size){
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_vouch v')->where($map)->count('v.id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="10";
	}
	
	$type_arr =C('BORROW_TYPE');
	$field = "v.*,b.borrow_name,b.borrow_status,b.total,b.has_pay,b.borrow_duration,b.borrow_money,b.has_borrow,b.has_vouch,b.repayment_type,m.user_name as borrow_user,m.credits";
	$list = M('borrow_vouch v')->field($field)->join("{$pre}borrow_info b ON b.id=v.borrow_id")->join("{$pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->select();
	$status_arr = array(
		"3"=>'标未满，流标',
		"1"=>'初审未通过',
		"5"=>'复审未通过'
	);
	foreach($list as $key=>$v){
		$list[$key]['borrow_progress'] = ceil($v['has_borrow']/$v['borrow_money']);
		$list[$key]['vouch_progress'] = ceil($v['has_vouch']/$v['borrow_money']);
		if($map['v.status']==2) $list[$key]['reason'] = $status_arr[$v['borrow_status']];
	}
	
	$row=array();
	$row['list'] = $list;
	$row['page'] = $page;
	return $row;
}

