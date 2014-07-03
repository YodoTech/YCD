<?php
// 全局设置
class CustomerAction extends ACommonAction
{
    /**
    +-----------------------s-----------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$map=array();
		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['add_time'] = array("between",$timespan);
			$search['start_time'] = strtotime(urldecode($_REQUEST['start_time']));	
			$search['end_time'] = strtotime(urldecode($_REQUEST['end_time']));	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(intval($_GET['kf'])>0){
			$auid = intval($_GET['kf']);
			$this->assign('kfname', get_admin_name(intval($_GET['kf'])));
			$search['kf'] = $auid;	
		}else{
			$auid = 1000000;
			$this->assign('kfname',"");
		}

		//borrowlist
		$map_borrow["borrow_uid"] = array("exp","in(select id from {$this->pre}members where customer_id ={$auid} AND user_leve=1)");
		$map_borrow["borrow_status"] = array("gt",5);
		if($map['add_time']) $map_borrow["add_time"] = $map['add_time'];
		
		$_borrow_list = M("borrow_info")->field("sum(borrow_money) as borrow_money,borrow_uid")->where($map_borrow)->group("borrow_uid")->select();

		$all_list = array();
		foreach($_borrow_list as $key=>$v){
			$all_list[$v['borrow_uid']]['borrow_money'] = $v['borrow_money'];
			$all_list[$v['borrow_uid']]['investor_capital'] = 0;
		}

		//invester
		$map_investor["investor_uid"] = array("exp","in(select id from {$this->pre}members where customer_id={$auid} AND user_leve=1)");
		$map_investor["status"] = array("gt",3);
		if($map['add_time']) $map_investor["add_time"] = $map['add_time'];
		$_invest_list = M("borrow_investor")->field('sum(investor_capital) as investor_capital,investor_uid')->where($map_investor)->group("investor_uid")->select();
		foreach($_invest_list as $skey=>$sv){
			$all_list[$sv['investor_uid']]['investor_capital'] = $sv['investor_capital'];
			$all_list[$sv['investor_uid']]['borrow_money'] = floatval($borrow_list[$sv['investor_uid']]['borrow_money']);
		}
		
		foreach($all_list as $keyx=>$vx){
			$all_list[$keyx]['id'] = $keyx;
			$all_list[$keyx]['user_name'] = M("members")->getFieldById($keyx,'user_name');
		}
		//会员统计
		$this->assign("search",$search);
		$this->assign('list',$all_list);
		$kflist = M("ausers")->where("is_kf=1")->getField('id,user_name');
      	$this->assign("kflist",$kflist);
	  
		$this->display();
    }
	
	private function getSon($id){
		if($id==1) $arr = M("area")->where("is_open=1")->getField("id as tid,id");
		else $arr = M("area")->where("reid in({$id}) AND is_open=1")->getField("id as tid,id");
		if(!empty($arr)){
			if($this->areaStr==NULL) $this->areaStr = implode(",",$arr);
			else  $this->areaStr .= ",".implode(",",$arr);
			$xid = implode(",",$arr);
			$this->getSon($xid);
		}
	}
	
	private function getSonCustomer($id){
		$arr = M("area")->where("reid in({$id})")->getField("id as tid,id");
		if(!empty($arr)){
			if($this->areaStr==NULL) $this->areaStr = implode(",",$arr);
			else  $this->areaStr .= ",".implode(",",$arr);
			$xid = implode(",",$arr);
			$this->getSon($xid);
		}
	}
	
}
?>