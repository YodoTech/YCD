<?php
// 本类由系统自动生成，仅供测试用途
class InvestAction extends HCommonAction {
	public function index() {
		$this->display();
	}
	//散标投资列表
    public function loanlist(){
		static $newpars;
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$per = C('DB_PREFIX');

		$curl = $_SERVER['REQUEST_URI'];
		$urlarr = parse_url($curl);
		parse_str($urlarr['query'],$surl);//array获取当前链接参数，2.
		
		$urlArr = array('borrow_name','borrow_status','repayment_type','borrow_use','money_from','money_to','borrow_duration','is_reward','province','city','area');
		$maprow = array();
		$searchMap =  array();
		foreach($urlArr as $vs){
			$maprow[$vs] = text($surl[$vs]);
		}
		//searchMap
		if(in_array($maprow['borrow_status'],array(2,3,4,6,7,8,9))){
			if($maprow['borrow_status']==9){
				$searchMap['borrow_status']=array("in",'2,4,6,7');
			}else{
				$searchMap['borrow_status']=$maprow['borrow_status'];
			}
		}else{
			$searchMap['borrow_status']=array("in",'2,4');
		}
		if(!empty($maprow['borrow_name'])) $searchMap['b.borrow_name'] = array("like","%{$maprow['borrow_name']}%");
		if(!empty($maprow['repayment_type'])) $searchMap['b.repayment_type'] =intval($maprow['repayment_type']);
		if(!empty($maprow['borrow_use'])) $searchMap['b.borrow_use'] =intval($maprow['borrow_use']);
		if($maprow['money_from'] < $maprow['money_to']){
			$bt = intval($maprow['money_from']).",".intval($maprow['money_to']);
			$searchMap['b.borrow_money'] =array("between","{$bt}");
		}
		if(!empty($maprow['borrow_duration'])) $searchMap['b.borrow_duration'] =intval($maprow['borrow_duration']);
		if(!empty($maprow['is_reward'])) $searchMap['b.is_reward'] =intval($maprow['is_reward']);
		if(!empty($maprow['province'])) $searchMap['b.province'] =intval($maprow['province']);
		if(!empty($maprow['city'])) $searchMap['b.city'] =intval($maprow['city']);
		if(!empty($maprow['area'])) $searchMap['b.area'] =intval($maprow['area']);
		//searchMap
		//if(is_array($searchMap['borrow_status'])) $searchMap['collect_time']=array('gt',time());
		if($maprow['borrow_status']==''){
			$searchMap['borrow_status']=array("in",'2,4,6,7');
		}
		$parm['map'] = $searchMap;
		$parm['pagesize'] = 4;
		//排序
		(strtolower($_GET['sort'])=="asc")?$sort="desc":$sort="asc";
		unset($surl['orderby'],$surl['sort']);
		$orderUrl = http_build_query($surl);
		if($_GET['orderby']){
			if(strtolower($_GET['orderby'])=="credits") $parm['orderby'] = "m.credits ".text($_GET['sort']);
			elseif(strtolower($_GET['orderby'])=="rate") $parm['orderby'] = "b.borrow_interest_rate ".text($_GET['sort']);
			elseif(strtolower($_GET['orderby'])=="borrow_money") $parm['orderby'] = "b.borrow_money ".text($_GET['sort']);
			else $parm['orderby']="b.id DESC";
		}else{
			$parm['orderby']="b.borrow_status ASC,b.id DESC";
		}
		$Sorder['Corderby'] = strtolower(text($_GET['orderby']));
		$Sorder['Csort'] = strtolower(text($_GET['sort']));
		$Sorder['url'] = $orderUrl;
		$Sorder['sort'] = $sort;
		$Sorder['orderby'] = text($_GET['orderby']);
		//排序
		
		$list = getBorrowList($parm);
		
		$this->assign("Sorder",$Sorder);
		$this->assign("searchMap",$maprow);
		$this->assign("Bconfig",$Bconfig);
		$this->assign("list",$list);
		
		
		if($maprow['borrow_status']==10){//逾期黑名单列表
			$map=array();
		
		//$map['_string'] = ' (d.repayment_time=0 AND d.deadline between 100000 and '.time().' AND d.status=7) ';
		$map['_string'] = ' d.repayment_time=0 AND d.deadline <'.time().' AND d.status=7 ';
		
		//分页处理
		import("ORG.Util.Page");
		$xcount = M('investor_detail d')->field("d.id")->where($map)->group('d.borrow_uid')->buildSql();
		$newxsql = M()->query("select count(*) as tc from {$xcount} as t");
		$count = $newxsql[0]['tc'];
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field = "count(*) as num,(sum(d.capital)+sum(d.interest)) as capital_all,d.borrow_uid,d.status,d.total,d.borrow_id,sum(d.substitute_money) as substitute_money,d.deadline";
		$buildSql = M('investor_detail d')->field($field)->where($map)->group('d.sort_order,d.borrow_id')->buildSql();
		$list = M()->query("select m.user_name,m.credits,m.id as uid,info.real_name,info.sex,info.idcard,info.zy,m.user_email,m.user_phone,b.province,b.city,b.area,count(*) as tc,t.deadline,sum(t.capital_all) as total_expired,t.borrow_uid as id,t.borrow_id from {$buildSql} as t  left join ".$pre."members m ON m.id=t.borrow_uid left join ".$pre."member_info info ON m.id=info.uid left join ".$pre."borrow_info b ON b.id=t.borrow_id group by t.borrow_uid limit {$Lsql}");
		
		$list = $this->_tendlist($list);
		$this->assign("tendbreak", $list);
		}
		$this->display();
    }
	/////////////////////////////////////////////////////////////////////////////////////
	private function _tendlist($list){
		$areaList = getArea();
		$row=array();
		foreach($list as $key=>$v){
			$v['location'] = $areaList[$v['province']].$areaList[$v['city']].$areaList[$v['area']];
			$v['breakday'] = getExpiredDays($v['deadline']);
			$v['expired_money'] = getExpiredMoney($v['breakday'],$v['total_expired'],$v['interest']);
			$v['call_fee'] = getExpiredCallFee($v['breakday'],$v['total_expired'],$v['interest']);
			$row[$key]=$v;
		}
		return $row;
	}
	
	////////////////////////////////////////////////////////////////////////////////////
    public function detail(){
		if($_GET['type']=='commentlist'){
			//评论
			$cmap['tid'] = intval($_GET['id']);
			$clist = getCommentList($cmap,5);
			$this->assign("commentlist",$clist['list']);
			$this->assign("commentpagebar",$clist['page']);
			$this->assign("commentcount",$clist['count']);
			$data['html'] = $this->fetch('commentlist');
			exit(json_encode($data));
		}


		$pre = C('DB_PREFIX');
		$id = intval($_GET['id']);
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		
		//合同ID
		if($this->uid){
			$invs = M('borrow_investor')->field('id')->where("borrow_id={$id} AND (investor_uid={$this->uid} OR borrow_uid={$this->uid})")->find();
			if($invs['id']>0) $invsx=$invs['id'];
			elseif(!is_array($invs)) $invsx='no';
		}else{
			$invsx='login';
		}
		$this->assign("invid",$invsx);
		//合同ID
		//borrowinfo
		$borrowinfo = M("borrow_info")->field(true)->find($id);
		if(!is_array($borrowinfo) || ($borrowinfo['borrow_status']==0 && $this->uid!=$borrowinfo['borrow_uid']) ) $this->error("数据有误");
		$borrowinfo['biao'] = $borrowinfo['borrow_times'];
		$borrowinfo['need'] = $borrowinfo['borrow_money'] - $borrowinfo['has_borrow'];
		$borrowinfo['lefttime'] =$borrowinfo['collect_time'] - time();
		$borrowinfo['progress'] = getFloatValue($borrowinfo['has_borrow']/$borrowinfo['borrow_money']*100,2);
		$borrowinfo['vouch_progress'] = getFloatValue($borrowinfo['has_vouch']/$borrowinfo['borrow_money']*100,2);
		$borrowinfo['vouch_need'] = $borrowinfo['borrow_money'] - $borrowinfo['has_vouch'];
		
		$this->assign("vo",$borrowinfo);
		//borrowinfo
		//此标借款利息还款相关情况
		if($borrowinfo['repayment_type']==2){
			$money = 100;
			$rate = $borrowinfo['borrow_interest_rate'];
			$month = $borrowinfo['borrow_duration'];
			
			$monthData['money'] = $money;
			$monthData['year_apr'] = $rate;
			$monthData['duration'] = $month;
			$monthData['type'] = "all";
			$repay_detail = EqualMonth($monthData);
			$this->assign('repay_detail',$repay_detail);
		}elseif($borrowinfo['repayment_type']==3){
			$money = 100;
			$rate = $borrowinfo['borrow_interest_rate'];
			$month = $borrowinfo['borrow_duration'];
			
			$monthData['account'] = $money;
			$monthData['year_apr'] = $rate;
			$monthData['month_times'] = $month;
			$monthData['type'] = "all";
			$repay_detail = EqualSeason($monthData);
			$this->assign('repay_detail',$repay_detail);
		}elseif($borrowinfo['repayment_type'] == 4){
			$money = 100;
			$rate = $borrowinfo['borrow_interest_rate'];
			$month = $borrowinfo['borrow_duration'];
			$parm['month_times'] = $month;
			$parm['account'] = $money;
			$parm['year_apr'] = $rate;
			$parm['type'] = "all";
			$repay_detail = EqualEndMonth($parm);
			$repay_detail['repayment_money'] = $repay_detail['repayment_account'];
			$this->assign( "repay_detail", $repay_detail );
		}elseif($borrowinfo['repayment_type']==1){
			$repay_detail['repayment_money'] = getFloatValue(100+100*$borrowinfo['borrow_interest_rate']*$borrowinfo['borrow_duration']/100,2);
			$this->assign('repay_detail',$repay_detail);
		}
		//此标借款利息还款相关情况
		//memberinfo
		$memberinfo = M("members m")->field("m.id,m.customer_name,m.customer_id,m.user_name,m.reg_time,m.credits,fi.*,mi.*")->join("{$pre}member_financial_info fi ON fi.uid = m.id")->join("{$pre}member_info mi ON mi.uid = m.id")->where("m.id={$borrowinfo['borrow_uid']}")->find();
		$areaList = getArea();
		$memberinfo['location'] = $areaList[$memberinfo['province']].$areaList[$memberinfo['city']];
		$memberinfo['location_now'] = $areaList[$memberinfo['province_now']].$areaList[$memberinfo['city_now']];
		$this->assign("minfo",$memberinfo);
		//memberinfo
		//vouch_list
		$vouch_list = M("borrow_vouch")->field(true)->where("borrow_id={$id}")->select();
		$this->assign("vouch_list",$vouch_list);
		$this->assign("Vstatus",array(0=>'担保中',1=>"担保完成"));
		//vouch_list
		
		//data_list
		$data_list = M("member_data_info")->field('type,add_time,count(status) as num,sum(deal_credits) as credits')->where("uid={$borrowinfo['borrow_uid']} AND status=1")->group('type')->select();
		$this->assign("data_list",$data_list);
		//data_list
		
		//paying_list
		$paying_list = getMemberBorrow($borrowinfo['borrow_uid']);
		$this->assign("paying_list",$paying_list);
		//paying_list

		//近期还款的投标
		//$time1 = microtime(true)*1000;
		$history = getDurationCount($borrowinfo['borrow_uid']);
		$this->assign("history",$history);
		//$time2 = microtime(true)*1000;
		//echo $time2-$time1;

		//investinfo
		$fieldx = "bi.investor_capital,bi.add_time,m.user_name,bi.is_auto";
		$investinfo = M("borrow_investor bi")->field($fieldx)->join("{$pre}members m ON bi.investor_uid = m.id")->where("bi.borrow_id={$id}")->order("bi.id DESC")->select();
		$this->assign("investinfo",$investinfo);
		//investinfo
		
		//帐户资金情况
		$this->assign("mainfo", getMinfo($borrowinfo['borrow_uid'],true));
		$this->assign("capitalinfo", getMemberBorrowScan($borrowinfo['borrow_uid']));
		//帐户资金情况
		
		//评论
		$cmap['tid'] = $id;
		$clist = getCommentList($cmap,5);
		$this->assign("Bconfig",$Bconfig);
		$this->assign("commentlist",$clist['list']);
		$this->assign("commentpagebar",$clist['page']);
		$this->assign("commentcount",$clist['count']);
		$this->display();
    }
	
	public function investcheck(){
		$pre = C('DB_PREFIX');
		if(!$this->uid) ajaxmsg('',3);
		$pin = md5($_POST['pin']);
		$borrow_id = intval($_POST['borrow_id']);
		$money = intval($_POST['money']);
		$vm = getMinfo($this->uid);
		$amoney = $vm['account_money']+$vm['back_money'];
		$uname = session('u_user_name');
		$pin_pass = $vm['pin_pass'];
		$amoney = floatval($amoney);
		
		$binfo = M("borrow_info")->field('borrow_money,has_borrow,has_vouch,borrow_max,borrow_min,borrow_type,password')->find($borrow_id);
		if($binfo['has_vouch']<$binfo['borrow_money'] && $binfo['borrow_type'] == 2) ajaxmsg("此标担保还未完成，您可以担保此标或者等担保完成再投标",3);
		if(!empty($binfo['password'])){
			if(empty($_POST['borrow_pass'])) ajaxmsg("此标是定向标，必须验证投标密码",3);
			else if($binfo['password']<>md5($_POST['borrow_pass'])) ajaxmsg("投标密码不正确",3);
		}
		//投标总数检测
		$capital = M('borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');
		if(($capital+$money)>$binfo['borrow_max']&&$binfo['borrow_max']>0){
			$xtee = $binfo['borrow_max'] - $capital;
			ajaxmsg("您已投标{$capital}元，此投上限为{$binfo['borrow_max']}元，你最多只能再投{$xtee}",3);
		}
		
		$need = $binfo['borrow_money'] - $binfo['has_borrow'];
		$caninvest = $need - $binfo['borrow_min'];
		if( $money>$caninvest && ($need-$money)<>0 ){
			$msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".($need-$money)."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>或者投标金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";
			if($caninvest<$binfo['borrow_min']) $msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".($need-$money)."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>即投标金额必须<font color='#FF0000'>等于{$need}元</font>";

			ajaxmsg($msg,3);
		}
		
		if(($need-$money)<0 ){
			$this->error("尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元",3);
		}
		
		if($pin<>$pin_pass) ajaxmsg("支付密码错误，请重试",0);
		if($money>$amoney){
			$msg = "尊敬的{$uname}，您准备投标{$money}元，但您的账户可用余额为{$amoney}元，您要先去充值吗？";
			ajaxmsg($msg,2);
		}else{
			$msg = "尊敬的{$uname}，您的账户可用余额为{$amoney}元，您确认投标{$money}元吗？";
			ajaxmsg($msg,1);
		}
	}
		
	public function investmoney(){
		if(!$this->uid) exit;
		/****** 防止模拟表单提交 *********/
		$cookieKeyS = cookie(strtolower(MODULE_NAME)."-invest");
		if($cookieKeyS!=$_REQUEST['cookieKey']){
			
		}
		/****** 防止模拟表单提交 *********/
		$money = intval($_POST['money']);
		$borrow_id = intval($_POST['borrow_id']);
		$m = M("member_money")->field('account_money,back_money')->find($this->uid);
		$amoney = $m['account_money']+$m['back_money'];
		$uname = session('u_user_name');
		if($amoney<$money) $this->error("尊敬的{$uname}，您准备投标{$money}元，但您的账户可用余额为{$amoney}元，请先去充值再投标.");
		
		$vm = getMinfo($this->uid);
		$pin_pass = $vm['pin_pass'];
		$pin = md5($_POST['pin']);
		if($pin<>$pin_pass) $this->error("支付密码错误，请重试");

		$binfo = M("borrow_info")->field('borrow_money,borrow_max,has_borrow,has_vouch,borrow_type,borrow_min')->find($borrow_id);
		//投标总数检测
		$capital = M('borrow_investor')->where("borrow_id={$borrow_id} AND investor_uid={$this->uid}")->sum('investor_capital');
		if(($capital+$money)>$binfo['borrow_max']&&$binfo['borrow_max']>0){
			$xtee = $binfo['borrow_max'] - $capital;
			$this->error("您已投标{$capital}元，此投上限为{$binfo['borrow_max']}元，你最多只能再投{$xtee}",3);
		}
		if($binfo['has_vouch']<$binfo['borrow_money'] && $binfo['borrow_type'] == 2) $this->error("此标担保还未完成，您可以担保此标或者等担保完成再投标");
		$need = $binfo['borrow_money'] - $binfo['has_borrow'];
		$caninvest = $need - $binfo['borrow_min'];
		if( $money>$caninvest && ($need-$money)<>0 ){
			$msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".($need-$money)."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>或者投标金额必须<font color='#FF0000'>小于等于{$caninvest}元</font>";
			if($caninvest<$binfo['borrow_min']) $msg = "尊敬的{$uname}，此标还差{$need}元满标,如果您投标{$money}元，将导致最后一次投标最多只能投".($need-$money)."元，小于最小投标金额{$binfo['borrow_min']}元，所以您本次可以选择<font color='#FF0000'>满标</font>即投标金额必须<font color='#FF0000'>等于{$need}元</font>";

			$this->error($msg);
		}
		if(($need-$money)<0 ){
			$this->error("尊敬的{$uname}，此标还差{$need}元满标,您最多只能再投{$need}元",3);
		}else{
			$done = investMoney($this->uid,$borrow_id,$money);
		}
		
		//$this->display("Public:_footer");
		//$this->assign("waitSecond",1000);
		if($done===true) {
			$this->success("恭喜成功投标{$money}元");
		}else if($done){
			$this->error($done);
		}else{
			$this->error("对不起，投标失败，请重试!");
		}
	}

	public function addcomment(){
		$data['comment'] = text($_POST['comment']);
		if(!$this->uid)  ajaxmsg("请先登陆",0);
		if(empty($data['comment']))  ajaxmsg("留言内容不能为空",0);
		$data['type'] = 1;
		$data['add_time'] = time();
		$data['uid'] = $this->uid;
		$data['uname'] = session("u_user_name");
		$data['tid'] = intval($_POST['tid']);
		$data['name'] = M('borrow_info')->getFieldById($data['tid'],'borrow_name');
		$newid = M('comment')->add($data);
		//$this->display("Public:_footer");
		if($newid) ajaxmsg();
		else ajaxmsg("留言失败，请重试",0);
	}
	
	public function jubao(){
		if($_POST['checkedvalue']){
			$data['reason'] = text($_POST['checkedvalue']);
			$data['text'] = text($_POST['thecontent']);
			$data['uid'] = $this->uid;
			$data['uemail'] = text($_POST['uemail']);
			$data['b_uid'] = text($_POST['b_uid']);
			$data['b_uname'] = text($_POST['theuser']);
			$data['add_time'] = time();
			$data['add_ip'] = get_client_ip();
			$newid = M('jubao')->add($data);
			if($newid) exit("1");
			else exit("0");
		}else{
			$id=intval($_GET['id']);
			$u['id'] = $id;
			$u['uname']=M('members')->getFieldById($id,"user_name");
			$u['uemail']=M('members')->getFieldById($this->uid,"user_email");
			$this->assign("u",$u);
			$data['content'] = $this->fetch("Public:jubao");
			exit(json_encode($data));
		}
	}
	
	public function ajax_invest(){
		if(!$this->uid) {
			ajaxmsg("请先登陆",0);
		}
		/****** 防止模拟表单提交 *********/
		$cookieTime = 15*3600;
		$cookieKey=md5(MODULE_NAME."-".time());
		cookie(strtolower(MODULE_NAME)."-invest",$cookieKey,$cookieTime);
		$this->assign("cookieKey",$cookieKey);
		/****** 防止模拟表单提交 *********/
		$pre = C('DB_PREFIX');
		$id=intval($_GET['id']);
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$field = "id,borrow_uid,borrow_money,borrow_status,borrow_type,has_borrow,has_vouch,borrow_interest_rate,borrow_duration,repayment_type,collect_time,borrow_min,borrow_max,password,borrow_use";
		$vo = M('borrow_info')->field($field)->find($id);
		if($this->uid == $vo['borrow_uid']) ajaxmsg("不能去投自己的标",0);
		if($vo['borrow_status'] <> 2) ajaxmsg("只能投正在借款中的标",0);
		//担保标检测
		if($vo['borrow_type']==2){
			if($vo['has_vouch']<$vo['borrow_money']) ajaxmsg("此标担保还未完成，您可以担保此标或者等担保完成再投标",0);
		}
		
		$vo['need'] = $vo['borrow_money'] - $vo['has_borrow'];
		if($vo['need']<0){
			ajaxmsg("投标金额不能超出借款剩余金额",0);
		}
		$vo['lefttime'] =$vo['collect_time'] - time();
		$vo['progress'] = getFloatValue($vo['has_borrow']/$vo['borrow_money']*100,4);//ceil($vo['has_borrow']/$vo['borrow_money']*100);
		$vo['uname'] = M("members")->getFieldById($vo['borrow_uid'],'user_name');
		$time1 = microtime(true)*1000;
		$vm = getMinfo($this->uid);
		$amoney = $vm['account_money']+$vm['back_money'];
		
		////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////
		
		if($amoney<floatval($vo['borrow_min'])){
			ajaxmsg("您的账户可用余额小于本标的最小投标金额限制，不能投标！",0);
		}elseif($amoney>=floatval($vo['borrow_max']) && floatval($vo['borrow_max'])>0){
			$toubiao = intval($vo['borrow_max']);
		}else if($amoney>=floatval($vo['need'])){
			$toubiao = intval($vo['need']);
		}else{
			$toubiao = floor($amoney);
		}
		$vo['toubiao'] =$toubiao;
		////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////
		$pin_pass = $vm['pin_pass'];
		$has_pin = (empty($pin_pass))?"no":"yes";
		$this->assign("has_pin",$has_pin);
		$this->assign("vo",$vo);
		$this->assign("account_money",$amoney);
		$this->assign("Bconfig",$Bconfig);
		$data['content'] = $this->fetch();
		ajaxmsg($data);
	}
	
	
	public function ajax_vouch(){
		/****** 防止模拟表单提交 *********/
		$cookieTime = 15*3600;
		$cookieKey=md5(MODULE_NAME."-".time());
		cookie(strtolower(MODULE_NAME)."-vouch",$cookieKey,$cookieTime);
		$this->assign("cookieKey",$cookieKey);
		/****** 防止模拟表单提交 *********/
		$pre = C('DB_PREFIX');
		$id=intval($_GET['id']);
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$field = "id,borrow_uid,borrow_money,has_borrow,borrow_interest_rate,borrow_duration,repayment_type,collect_time,has_vouch,reward_vouch_rate,borrow_min,borrow_max";
		$vo = M('borrow_info')->field($field)->find($id);
		
		$vo['need'] = $vo['borrow_money'] - $vo['has_borrow'];
		$vo['lefttime'] =$vo['collect_time'] - time();
		$vo['progress'] = getFloatValue($vo['has_borrow']/$vo['borrow_money']*100,4);//ceil($vo['has_borrow']/$vo['borrow_money']*100);
		$vo['vouch_progress'] = getFloatValue($vo['has_vouch']/$vo['borrow_money']*100,4);//ceil($vo['has_vouch']/$vo['borrow_money']*100);
		$vo['vouch_need'] = $vo['borrow_money'] - $vo['has_vouch'];
		$vo['uname'] = M("members")->getFieldById($vo['borrow_uid'],'user_name');
		$time1 = microtime(true)*1000;
		$vm = getMinfo($this->uid,"m.pin_pass,mm.invest_vouch_cuse");
		
		////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////
		if($vo['vouch_progress']==100){
			$amoney = $vm['account_money']+$vm['back_money'];
			if($amoney<floatval($vo['borrow_min'])){
				ajaxmsg("您的账户可用余额小于本标的最小投标金额限制，不能投标！",0);
			}elseif($amoney>=floatval($vo['borrow_max']) && floatval($vo['borrow_max'])>0){
				$toubiao = intval($vo['borrow_max']);
			}else if($amoney>=floatval($vo['need'])){
				$toubiao = intval($vo['need']);
			}else{
				$toubiao = floor($amoney);
			}
			$vo['toubiao'] =$toubiao;
		}
		////////////////////投标时自动填写可投标金额在投标文本框 2013-07-03 fan////////////////////////
		
		$pin_pass = $vm['pin_pass'];
		$has_pin = (empty($pin_pass))?"no":"yes";
		$this->assign("has_pin",$has_pin);
		$this->assign("vo",$vo);
		$this->assign("invest_vouch_cuse",$vm['invest_vouch_cuse']);
		$this->assign("Bconfig",$Bconfig);
		$data['content'] = $this->fetch();
		ajaxmsg($data,1);
	}
	
	public function vouchcheck(){
		$pre = C('DB_PREFIX');
		if(!$this->uid) ajaxmsg('',3);
		$pin = md5($_POST['pin']);
		$money = intval($_POST['vouch_money']);
		$vm = getMinfo($this->uid,"m.pin_pass,mm.invest_vouch_cuse");
		$amoney = $vm['invest_vouch_cuse'];
		$uname = session('user_name');
		$pin_pass = $vm['pin_pass'];
		$amoney = floatval($amoney);

		if($pin<>$pin_pass) ajaxmsg("支付密码错误，请重试",0);
		if($money>$amoney){
			$msg = "尊敬的{$uname}，您准备担保{$money}元，但您可用担保投资额度为{$amoney}元，要去申请更高额度吗？";
			ajaxmsg($msg,2);
		}else{
			$msg = "尊敬的{$uname}，您可用担保投资额度为{$amoney}元，您确认担保{$money}元吗？";
			ajaxmsg($msg,1);
		}
	}
		
	public function vouchmoney(){
		if(!$this->uid) exit;
			/****** 防止模拟表单提交 *********/
		$cookieKeyS = cookie(strtolower(MODULE_NAME)."-vouch");
		if($cookieKeyS!=$_REQUEST['cookieKey']){
			
		}
		/****** 防止模拟表单提交 *********/
		$money = intval($_POST['vouch_money']);
		$borrow_id = intval($_POST['borrow_id']);
		$rate = M('borrow_info')->getFieldById($borrow_id,'reward_vouch_rate');
		$amoney = M("member_money")->getFieldByUid($this->uid,'invest_vouch_cuse');
		$uname = session('u_user_name');
		if($amoney<$money) $this->error("尊敬的{$uname}，您准备担保{$money}元，但您可用担保投资额度为{$amoney}元，请先去申请更高额度.");
		
		$saveVouch['borrow_id'] = $borrow_id;
		$saveVouch['uid'] = $this->uid;
		$saveVouch['uname'] = $uname;
		$saveVouch['vouch_money'] = $money;
		$saveVouch['vouch_reward_rate'] = $rate;
		$saveVouch['vouch_reward_money'] = getFloatValue($money*$rate/100,2);
		$saveVouch['add_ip'] = get_client_ip();
		$saveVouch['vouch_time'] = time();
		$newid = M('borrow_vouch')->add($saveVouch);
		
		if($newid) $done = M("member_money")->where("uid={$this->uid}")->setDec('invest_vouch_cuse',$money);
		//$this->assign("waitSecond",1000);
		if($done==true){
			M("borrow_info")->where("id={$borrow_id}")->setInc('has_vouch',$money);
			$this->success("恭喜成功担保{$money}元");
		}
		else $this->error("对不起，担保失败，请重试!");
	}
	
	public function getarea(){
		$rid = intval($_GET['rid']);
		if(empty($rid)){
			$data['NoCity'] = 1;
			exit(json_encode($data));
		}
		$map['reid'] = $rid;
		$alist = M('area')->field('id,name')->order('sort_order DESC')->where($map)->select();

		if(count($alist)===0){
			$str="<option value=''>--该地区下无下级地区--</option>\r\n";
		}else{
			if($rid==1) $str.="<option value='0'>请选择省份</option>\r\n";
			foreach($alist as $v){
				$str.="<option value='{$v['id']}'>{$v['name']}</option>\r\n";
			}
		}
		$data['option'] = $str;
		$res = json_encode($data);
		echo $res;
	}	
	
	public function addfriend(){
		if(!$this->uid) ajaxmsg("请先登陆",0);
		$fuid = intval($_POST['fuid']);
		$type = intval($_POST['type']);
		if(!$fuid||!$type) ajaxmsg("提交的数据有误",0);
		
		$save['uid'] = $this->uid;
		$save['friend_id'] = $fuid;
		$vo = M('member_friend')->where($save)->find();	
		
		if($type==1){//加好友
		if($this->uid == $fuid) ajaxmsg("您不能对自己进行好友相关的操作",0);
			if(is_array($vo)){
				if($vo['apply_status']==3){
					$msg="已经从黑名单移至好友列表";
					$newid = M('member_friend')->where($save)->setField("apply_status",1);
				}elseif($vo['apply_status']==1){
					$msg="已经在你的好友名单里，不用再次添加";
				}elseif($vo['apply_status']==0){
					$msg="已经提交加好友申请，不用再次添加";
				}elseif($vo['apply_status']==2){
					$msg="好友申请提交成功";
					$newid = M('member_friend')->where($save)->setField("apply_status",0);
				}
			}else{
				$save['uid'] = $this->uid;
				$save['friend_id'] = $fuid;
				$save['apply_status'] = 0;
				$save['add_time'] = time();
				$newid = M('member_friend')->add($save);	
				$msg="好友申请成功";
			}
		}elseif($type==2){//加黑名单
		if($this->uid == $fuid) ajaxmsg("您不能对自己进行黑名单相关的操作",0);
			if(is_array($vo)){
				if($vo['apply_status']==3) $msg="已经在黑名单里了，不用再次添加";
				else{
					$msg="成功移至黑名单";
					$newid = M('member_friend')->where($save)->setField("apply_status",3);	
				}
			}else{
				$save['uid'] = $this->uid;
				$save['friend_id'] = $fuid;
				$save['apply_status'] = 3;
				$save['add_time'] = time();
				$newid = M('member_friend')->add($save);	
				$msg="成功加入黑名单";
			}
		}
		if($newid) ajaxmsg($msg);
		else ajaxmsg($msg,0);
	}
	
	
	public function innermsg(){
		if(!$this->uid) ajaxmsg("请先登陆",0);
		$fuid = intval($_GET['uid']);
		if($this->uid == $fuid) ajaxmsg("您不能对自己进行发送站内信的操作",0);
		$this->assign("touid",$fuid);
		$data['content'] = $this->fetch("Public:innermsg");
		ajaxmsg($data);
	}
	public function doinnermsg(){
		$touid = intval($_POST['to']);
		$msg = text($_POST['msg']);	
		$title = text($_POST['title']);	
		$newid = addMsg($this->uid,$touid,$title,$msg);
		if($newid) ajaxmsg();
		else ajaxmsg("发送失败",0);
		
	}

	public function plan() {
		//...
		$this->display();
	}

}