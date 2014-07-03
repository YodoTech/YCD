<?php
// 全局设置
class WithdrawlogingAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作 提现处理中
    +----------------------------------------------------------
    */
	public function index()
    {
		
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['w.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['w.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['deal_user']){
			$map['w.deal_user'] = urldecode($_REQUEST['deal_user']);
			$search['deal_user'] = $map['w.deal_user'];	
		}
		
		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['w.withdraw_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['w.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		
		//分页处理
		import("ORG.Util.Page");
		$map['w.withdraw_status'] =1;
		$count = M('member_withdraw w')->join("{$this->pre}members m ON m.id=w.uid")->where($map)->count('w.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$list = M('member_withdraw w')->field($field)->join("{$this->pre}members m ON w.uid=m.id")->join("{$this->pre}member_info mi ON w.uid=mi.uid")->join("{$this->pre}member_money mm on w.uid = mm.uid")->where($map)->order(' w.id DESC ')->limit($Lsql)->select();
		
		
		$listType = C('WITHDRAW_STATUS');
		unset($listType[0],$listType[2],$listType[3]);
		$this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
		$this->assign("status",$listType);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
    //编辑
    public function edit() {
        $model = M('member_withdraw');
        $id = intval($_REQUEST['id']);
        $vo = $model->find($id);
		//$vo['uname'] = M("members")->getFieldById($vo['uid'],'user_name');
	 	$listType = C('WITHDRAW_STATUS');
		unset($listType[0],$listType[1]);
		$this->assign('type_list',$listType);

		/*$vo['real_name'] = M("member_info")->getFieldByUid($vo['uid'],'real_name');
	
		$vo['bank_num'] = M("member_banks")->getFieldByUid($vo['uid'],'bank_num');
		$vo['bank_province'] = M("member_banks")->getFieldByUid($vo['uid'],'bank_province');
		$vo['bank_city'] = M("member_banks")->getFieldByUid($vo['uid'],'bank_city');
		$vo['bank_address'] = M("member_banks")->getFieldByUid($vo['uid'],'bank_address');
		$vo['bank_name'] = M("member_banks")->getFieldByUid($vo['uid'],'bank_name');*/
		
	 /////////////////////////////////
	 	$field= 'w.*,m.user_name,mi.real_name,w.id,w.uid,(mm.account_money+mm.back_money) all_money,mb.bank_num,mb.bank_province,mb.bank_city,mb.bank_address,mb.bank_name';
		$list = M('member_withdraw w')->field($field)->join("{$this->pre}members m ON w.uid=m.id")->join("{$this->pre}member_info mi ON w.uid=mi.uid")->join("{$this->pre}member_money mm on w.uid = mm.uid")->join("{$this->pre}member_banks mb on w.uid = mb.uid")->where("w.id=$id")->order(' w.id ASC ')->limit($Lsql)->select();
		foreach($list as $v){
			$vo['uname'] =$v['user_name'];
			$vo['real_name'] = $v['real_name'];
			$vo['bank_num'] =$v['bank_num'];
			$vo['bank_province'] = $v['bank_province'];
			$vo['bank_city'] =$v['bank_city'];
			$vo['bank_address'] = $v['bank_address'];
			$vo['bank_name'] =$v['bank_name'];
			$vo['all_money'] =$v['all_money'];
			$vo['withdraw_fee'] =$v['withdraw_fee'];
		}
	 //////////////////////////////////////
        $this->assign('vo', $vo);
        $this->display();
    }
	
	 public function doEdit() {
        $model = D("member_withdraw");
		$status = intval($_POST['withdraw_status']);
		$id = intval($_POST['id']);
		$deal_info = $_POST['deal_info'];
		$secondfee = floatval($_POST['withdraw_fee']);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
		$model->withdraw_status = $status;
		$model->deal_info = $deal_info;
		$model->deal_time=time();
		$model->deal_user=session('adminname');
		////////////////////////
		$field= 'w.*,w.id,w.uid,(mm.account_money+mm.back_money) all_money';
		$vo = M("member_withdraw w")->field($field)->join("{$this->pre}member_money mm on w.uid = mm.uid")->find($id);
		$um = M('members')->field("user_name,user_phone")->find($vo['uid']);
		if($vo['withdraw_status']<>3 && $status==3){
			addInnerMsg($vo['uid'],"您的提现申请审核未通过","您的提现申请审核未通过");
			if( ($vo['all_money']-$vo['second_fee'])<=0 ){
				  memberMoneyLog($vo['uid'],12,$vo['withdraw_money'],"提现未通过,返还,其中提现金额：".$vo['withdraw_money']."元，手续费：".$vo['second_fee']."元",'0','@网站管理员@',$vo['second_fee']);
			}else{
				 memberMoneyLog($vo['uid'],12,$vo['withdraw_money'],"提现未通过,返还",'0','@网站管理员@',$vo['second_fee']);
			}
			$model->success_money = 0;
			// memberMoneyLog($vo['uid'],12,$vo['withdraw_money']+$vo['withdraw_fee'],"提现未通过,返还",'0','@网站管理员@');
		}else if($vo['withdraw_status']<>2 && $status==2){
			addInnerMsg($vo['uid'],"您的提现已完成","您的提现已完成");
			if( ($vo['all_money'] - $vo['second_fee'])<0 ){
				memberMoneyLog($vo['uid'],29,-($vo['withdraw_money']-$vo['second_fee']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money']-$vo['second_fee'])."走上",'0','@网站管理员@',0);
				//$model->withdraw_money = $vo['withdraw_money']+$vo['withdraw_fee'];
				$model->success_money = $vo['withdraw_money']-$vo['second_fee'];
			}else{
				memberMoneyLog($vo['uid'],29,-($vo['withdraw_money']),"提现成功,扣除实际手续费".$vo['second_fee']."元，减去冻结资金，到帐金额".($vo['withdraw_money'])."走下",'0','@网站管理员@');
				$model->success_money = $vo['withdraw_money'];
			}
				
			SMStip("withdraw",$um['user_phone'],array("#USERANEM#","#MONEY#"),array($um['user_name'],($vo['withdraw_money']-$vo['second_fee'])));
		}elseif($vo['withdraw_status']<>1 && $status==1){
			addInnerMsg($vo['uid'],"您的提现申请已通过","您的提现申请已通过，正在处理中");
			
			if($vo['all_money']  <=$secondfee ){
				memberMoneyLog($vo['uid'],36,-($vo['withdraw_money']-$secondfee),"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money']-$secondfee)."元",'0','@网站管理员@',$vo['withdraw_fee']-$secondfee);
				$model->success_money = $vo['withdraw_money']-$secondfee;
			}else{
				memberMoneyLog($vo['uid'],36,-$vo['withdraw_money'],"提现申请已通过，扣除实际手续费".$secondfee."元，到帐金额".($vo['withdraw_money'])."元",'0','@网站管理员@',$vo['withdraw_fee']-$secondfee);
				$model->success_money = $vo['withdraw_money'];
			}

			$model->withdraw_fee = $vo['withdraw_fee'];
			$model->second_fee = $secondfee;
		}
		//////////////////////////
		$result = $model->save();
		
        if ($result) { //保存成功
          //成功提示
            $this->assign('jumpUrl', __URL__);
            $this->success(L('修改成功'));
        } else {
			//$this->assign("waitSecond",10000);
            //失败提示
            $this->error(L('修改失败'));
        }
    }
	
	public function _listFilter($list){
	 	$listType = C('WITHDRAW_STATUS');
		$row=array();
		foreach($list as $key=>$v){
			$v['withdraw_status'] = $listType[$v['withdraw_status']];
			$v['uname'] = M("members")->getFieldById($v['uid'],'user_name');
			$v['real_name'] = M("member_info")->getFieldById($v['uid'],'real_name');
			$row[$key]=$v;
		}
		return $row;
	}
}
?>