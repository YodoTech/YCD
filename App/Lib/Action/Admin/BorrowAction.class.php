<?php
// 全局设置
class BorrowAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function waitverify()
    {
		$map=array();
		$map['b.borrow_status'] = 0;
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.updata,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.add_time,m.user_name,m.id mid,b.is_tuijian';
		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
    public function waitverify2()
    {
		$map=array();
		$map['b.borrow_status'] = 4;
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.full_time,m.user_name,m.id mid,b.is_tuijian';
		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
    public function waitmoney()
    {
		$map=array();
		$map['b.borrow_status'] = 2;
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.add_time,m.user_name,m.id mid,b.is_tuijian';
		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
    public function repaymenting()
    {
		$map=array();
		$map['b.borrow_status'] = 6;//还款中
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.deadline,m.user_name,m.id mid,b.is_tuijian';
		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
    public function borrowbreak()
    {//暂时未处理
		$map['deadline'] = array("exp","<>0 AND deadline<".time()." AND `repayment_money`<`borrow_money`");
		$field= 'id,borrow_name,borrow_uid,borrow_duration,borrow_type,borrow_money,borrow_fee,repayment_money,b.updata,borrow_interest_rate,repayment_type,deadline';
		$this->_list(D('Borrow'),$field,$map,'id','DESC');
        $this->display();
    }
	
	public function unfinish(){
		$map=array();
		$map['b.borrow_status'] = 3;
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_status,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.deadline,m.id mid,m.user_name,v.deal_user_2,v.deal_time_2,v.deal_info_2';
		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->join("{$this->pre}borrow_verify v ON b.id=v.borrow_id")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
	}
	
	
    public function done()
    {
		$map=array();
		$map['b.borrow_status'] = array("in","7,9");
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.repayment_money,b.deadline,m.id mid,m.user_name';
		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
    public function fail()
    {
		$map=array();
		$map['b.borrow_status'] = 1;
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_status,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.add_time,m.user_name,v.deal_user,v.deal_time,m.id mid,v.deal_info';
		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->join("{$this->pre}borrow_verify v ON b.id=v.borrow_id")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
    public function fail2()
    {
		$map=array();
		$map['b.borrow_status'] = 5;
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_status,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.updata,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.add_time,m.user_name,m.id mid,v.deal_user_2,v.deal_time_2,v.deal_info_2';
		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->join("{$this->pre}borrow_verify v ON b.id=v.borrow_id")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
    public function _addFilter()
    {
		$typelist = get_type_leve_list('0','acategory');//分级栏目
		$this->assign('type_list',$typelist);
		
    }
	
    public function _editFilter($id)
    {
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$borrow_status = $Bconfig['BORROW_STATUS'];
		switch(strtolower(session('listaction'))){
			case "waitverify":
				for($i=0;$i<=10;$i++){
					if(in_array($i,array("1","2")) ) continue;
					unset($borrow_status[$i]);
				}
			break;
			case "waitverify2":
				for($i=0;$i<=10;$i++){
					if(in_array($i,array("5","6")) ) continue;
					unset($borrow_status[$i]);
				}
			break;
			case "waitmoney":
				for($i=0;$i<=10;$i++){
					if(in_array($i,array("2","3")) ) continue;
					unset($borrow_status[$i]);
				}
			break;
			case "fail":
				for($i=0;$i<=10;$i++){
					if(in_array($i,array("1","2")) ) continue;
					unset($borrow_status[$i]);
				}
				/*
				unset($borrow_status['3'],$borrow_status['4'],$borrow_status['5']);
				*/
			break;
		}
		///////////////////////////////////////////////////////////////////////////////////
		$danbao = M('article_category')->field('id,type_name')->where('parent_id=354')->select();
		$dblist = array();
		if(is_array($danbao)){
			foreach($danbao as $key => $v){
				$dblist[$v['id']]=$v['type_name'];
			}
		}
		$this->assign("danbao_list",$dblist);//新增担保标A+
		//////////////////////////////////////////////////////////////////////////////
		$this->assign('xact',session('listaction'));
		$btype = $Bconfig['REPAYMENT_TYPE'];
		$this->assign("vv",M("borrow_verify")->find($id));
		$this->assign('borrow_status',$borrow_status);
		$this->assign('type_list',$btype);
		$this->assign('borrow_type',$Bconfig['BORROW_TYPE']);
		//setBackUrl(session('listaction'));	
    }
	public function sRepayment(){
		$borrow_id = $_GET['id'];
		$binfo = M("borrow_info")->field("has_pay,total")->find($borrow_id);
		$from = $binfo['has_pay'] + 1;
		for($i=$from;$i<=$binfo['total'];$i++){
			$res = borrowRepayment($borrow_id,$i,2);
		}
		if($res===true) $this->success("代还成功");
		elseif(!empty($res)) $this->error($res);
		else{
			$this->error("代还出错，请重试");
		}
	}

	public function _doAddFilter($m){
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Article/' ;
			$this->thumbMaxWidth = C('ARTICLE_UPLOAD_W');
			$this->thumbMaxHeight = C('ARTICLE_UPLOAD_H');
			$info = $this->CUpload();
			$data['art_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['art_img']) $m->art_img=$data['art_img'];
		$m->art_time=time();
		if($_POST['is_remote']==1) $m->art_content = get_remote_img($m->art_content);
		return $m;
	}

	public function doEditWaitverify(){
        $m = D(ucfirst($this->getActionName()));
        if (false === $m->create()) {
            $this->error($m->getError());
        }
		$vm = M('borrow_info')->field('borrow_uid,borrow_status,borrow_type,first_verify_time,password,updata,danbao,vouch_money')->find($m->id);
		
		if($vm['borrow_status']==2 && $m->borrow_status<>2){
			$this->error('已通过初审的借款不能改为别的状态');
			exit;
		}
		////////////////////图片编辑///////////////////////
		if(!empty($_POST['swfimglist'])){
			foreach($_POST['swfimglist'] as $key=>$v){
				$row[$key]['img'] = substr($v,1);
				$row[$key]['info'] = $_POST['picinfo'][$key];
			}
			$m->updata=serialize($row);
		}
		////////////////////图片编辑///////////////////////
		
		if($vm['borrow_status']<>2 && $m->borrow_status==2){
		  //新标提醒
			//newTip($m->id);
			MTip('chk8',$vm['borrow_uid'],$m->id);
		  //自动投标
			if($m->borrow_type==1){
				memberLimitLog($vm['borrow_uid'],1,-($m->borrow_money),$info="{$m->id}号标初审通过");
			}elseif($m->borrow_type==2){
				memberLimitLog($vm['borrow_uid'],2,-($m->borrow_money),$info="{$m->id}号标初审通过");
			}
			$vss = M("members")->field("user_phone,user_name")->where("id = {$vm['borrow_uid']}")->find();
			SMStip("firstV",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$m->id));
		}
		//if($m->borrow_status==2) $m->collect_time = strtotime("+ {$m->collect_day} days");
		if($m->borrow_status==2){ 
			$m->collect_time = strtotime("+ {$m->collect_day} days");
			//$m->is_tuijian = 1;
		}
		$m->borrow_interest = getBorrowInterest($m->repayment_type,$m->borrow_money,$m->borrow_duration,$m->borrow_interest_rate);
        //保存当前数据对象
		if($m->borrow_status==2 || $m->borrow_status==1) $m->first_verify_time = time();
		else unset($m->first_verify_time);
		unset($m->borrow_uid);
		$bs = intval($_POST['borrow_status']);
        if ($result = $m->save()) { //保存成功
			if($bs==2 || $bs==1){
				$verify_info['borrow_id'] = intval($_POST['id']);
				$verify_info['deal_info'] = text($_POST['deal_info']);
				$verify_info['deal_user'] = $this->admin_id;
				$verify_info['deal_time'] = time();
				$verify_info['deal_status'] = $bs;
				if($vm['first_verify_time']>0) {
					M('borrow_verify')->save($verify_info);
					$did = $vm['id'];
				} else {
					$did = M('borrow_verify')->add($verify_info);
				}
				if ($did) {//日志记录
					m('data_verify_log')->add(array(
						'type' 			=> 'borrowverify1',
						'did' 			=> $did,
						'op_status' 	=> $verify_info['deal_status'],
						'op_info' 		=> $verify_info['deal_info'],
						'op_credits' 	=> 0,
						'op_uid' 		=> $verify_info['deal_user'],
						'op_time' 		=> time()
					));
				}
			}
			if($vm['borrow_status']<>2 && $_POST['borrow_status']==2 && empty($vm['password'])==true) autoInvest(intval($_POST['id']));
			//if($vm['borrow_status']<>2 && $_POST['borrow_status']==2)) autoInvest(intval($_POST['id']));
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
            //失败提示
            $this->error(L('修改失败'));
		}	
	}

	public function doEditWaitverify2(){
        $m = D(ucfirst($this->getActionName()));
        if (false === $m->create()) {
            $this->error($m->getError());
        }
		$vm = M('borrow_info')->field('borrow_uid,borrow_money,borrow_status,first_verify_time,updata,danbao,vouch_money,borrow_fee')->find($m->id);
		if($m->borrow_status<>5 && $m->borrow_status<>6){
			$this->error('已经满标的的借款只能改为复审通过或者复审未通过');
			exit;
		}
		if($vm['borrow_fee'] <> $m->borrow_fee){
			$this->error('复审中的借款不能再更改‘借款管理费’');
			exit;
		}

		////////////////////图片编辑///////////////////////
		if(!empty($_POST['swfimglist'])){
			foreach($_POST['swfimglist'] as $key=>$v){
				$row[$key]['img'] = substr($v,1);
				$row[$key]['info'] = $_POST['picinfo'][$key];
			}
			$m->updata=serialize($row);
		}
		////////////////////图片编辑///////////////////////
		//复审投标检测
		//$capital_sum1=M('investor_detail')->where("borrow_id={$m->id}")->sum('capital');
		$capital_sum2=M('borrow_investor')->where("borrow_id={$m->id}")->sum('investor_capital');
		if(($vm['borrow_money']!=$capital_sum2)){
			$this->error('投标金额不统一，请确认！');
			exit;
		}
		if($m->borrow_status==6){//复审通过
			$appid = borrowApproved($m->id);
			if(!$appid) $this->error("复审失败");
		  //新标提醒
			//newTip($m->id);
			MTip('chk9',$vm['borrow_uid'],$m->id);
		  //自动投标
			
			$vss = M("members")->field("user_phone,user_name")->where("id = {$vm['borrow_uid']}")->find();
			SMStip("approve",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$m->id));
		    //autoInvest($result);
		}elseif($m->borrow_status==5){//复审未通过
			$appid = borrowRefuse($m->id,3);
			if(!$appid) $this->error("复审失败");
			MTip('chk12',$vm['borrow_uid'],$m->id);
		}
        //保存当前数据对象
		$m->second_verify_time = time();
		unset($m->borrow_uid);
		$bs = intval($_POST['borrow_status']);
        if ($result = $m->save()) { //保存成功
				$verify_info['borrow_id'] = intval($_POST['id']);
				$verify_info['deal_info_2'] = text($_POST['deal_info_2']);
				$verify_info['deal_user_2'] = $this->admin_id;
				$verify_info['deal_time_2'] = time();
				$verify_info['deal_status_2'] = $bs;
				if($vm['first_verify_time']>0) M('borrow_verify')->save($verify_info);
				else  M('borrow_verify')->add($verify_info);
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
            //失败提示
            $this->error(L('修改失败'));
		}	
	}

	public function doEditWaitmoney(){
        $m = D(ucfirst($this->getActionName()));
        if (false === $m->create()) {
            $this->error($m->getError());
        }
		
		$vm = M('borrow_info')->field('borrow_uid,borrow_type,borrow_money,first_verify_time,borrow_interest_rate,borrow_duration,repayment_type,collect_day,collect_time,borrow_fee')->find($m->id);
		if($vm['borrow_money']<>$m->borrow_money ||
			 $vm['borrow_interest_rate']<>$m->borrow_interest_rate ||
			 $vm['borrow_duration']<>$m->borrow_duration ||
			 $vm['borrow_type']<>$m->borrow_type ||
			 $vm['repayment_type']<>$m->repayment_type ||
			 $vm['borrow_fee'] <> $m->borrow_fee
		  ){
			
		}

		//招标中的借款流标
		if($m->borrow_status==3){
			//流标返回
			$appid = borrowRefuse($m->id,2);
			if(!$appid) $this->error("流标失败");
			MTip('chk11',$vm['borrow_uid'],$m->id);
			$m->second_verify_time = time();
			//流标操作相当于复审
			$verify_info['borrow_id'] = $m->id;
			$verify_info['deal_info_2'] = text($_POST['deal_info_2']);
			$verify_info['deal_user_2'] = $this->admin_id;
			$verify_info['deal_time_2'] = time();
			$verify_info['deal_status_2'] = $m->borrow_status;
			if($vm['first_verify_time']>0) M('borrow_verify')->save($verify_info);
			else  M('borrow_verify')->add($verify_info);
			
			$vss = M("members")->field("user_phone,user_name")->where("id = {$vm['borrow_uid']}")->find();
			SMStip("refuse",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$verify_info['borrow_id']));

		}else{
			if($vm['collect_day'] < $m->collect_day){
				$spanday = $m->collect_day-$vm['collect_day'];
				$m->collect_time = strtotime("+ {$spanday} day",$vm['collect_time']);
			}
			unset($m->second_verify_time);	
		}
		
        //保存当前数据对象
 		unset($m->borrow_uid);
		////////////////////图片编辑///////////////////////
		foreach($_POST['swfimglist'] as $key=>$v){
			$row[$key]['img'] = substr($v,1);
			$row[$key]['info'] = $_POST['picinfo'][$key];
		}
		$m->updata=serialize($row);
		////////////////////图片编辑///////////////////////
       if ($result = $m->save()) { //保存成功
	   		//$this->assign("waitSecond",10000);
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
            //失败提示
            $this->error(L('修改失败'));
		}	
	}
	

	public function doEditFail(){
        $m = D(ucfirst($this->getActionName()));
        if (false === $m->create()) {
            $this->error($m->getError());
        }
		$vm = M('borrow_info')->field('borrow_uid,borrow_status,borrow_type,first_verify_time,password,updata,danbao,vouch_money')->find($m->id);
		
		if($vm['borrow_status']==2 && $m->borrow_status<>2){
			$this->error('已通过初审的借款不能改为别的状态');
			exit;
		}
		////////////////////图片编辑///////////////////////
		if(!empty($_POST['swfimglist'])){
			foreach($_POST['swfimglist'] as $key=>$v){
				$row[$key]['img'] = substr($v,1);
				$row[$key]['info'] = $_POST['picinfo'][$key];
			}
			$m->updata=serialize($row);
		}
		////////////////////图片编辑///////////////////////
		
		if($vm['borrow_status']<>2 && $m->borrow_status==2){
		  //新标提醒
			MTip('chk8',$vm['borrow_uid'],$m->id);
		  //自动投标
			if($m->borrow_type==1){
				memberLimitLog($vm['borrow_uid'],1,-($m->borrow_money),$info="{$m->id}号标初审通过");
			}elseif($m->borrow_type==2){
				memberLimitLog($vm['borrow_uid'],2,-($m->borrow_money),$info="{$m->id}号标初审通过");
			}
			$vss = M("members")->field("user_phone,user_name")->where("id = {$vm['borrow_uid']}")->find();
			SMStip("firstV",$vss['user_phone'],array("#USERANEM#","ID"),array($vss['user_name'],$m->id));
		}
		if($m->borrow_status==2){ 
			$m->collect_time = strtotime("+ {$m->collect_day} days");
		}
		$m->borrow_interest = getBorrowInterest($m->repayment_type,$m->borrow_money,$m->borrow_duration,$m->borrow_interest_rate);
        //保存当前数据对象
		if($m->borrow_status==2 || $m->borrow_status==1) $m->first_verify_time = time();
		else unset($m->first_verify_time);
		unset($m->borrow_uid);
		$bs = intval($_POST['borrow_status']);
        if ($result = $m->save()) { //保存成功
			if($bs==2 || $bs==1){
				$verify_info['borrow_id'] = intval($_POST['id']);
				$verify_info['deal_info'] = text($_POST['deal_info']);
				$verify_info['deal_user'] = $this->admin_id;
				$verify_info['deal_time'] = time();
				$verify_info['deal_status'] = $bs;
				if($vm['first_verify_time']>0) {
					M('borrow_verify')->save($verify_info);
					$did = $vm['id'];
				} else {
					$did = M('borrow_verify')->add($verify_info);
				}
				if ($did) {//日志记录
					m('data_verify_log')->add(array(
						'type' 			=> 'borrowverify1',
						'did' 			=> $did,
						'op_status' 	=> $verify_info['deal_status'],
						'op_info' 		=> $verify_info['deal_info'],
						'op_credits' 	=> 0,
						'op_uid' 		=> $verify_info['deal_user'],
						'op_time' 		=> time()
					));
				}
			}
			if($vm['borrow_status']<>2 && $_POST['borrow_status']==2 && empty($vm['password'])==true) autoInvest(intval($_POST['id']));
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
            //失败提示
            $this->error(L('修改失败'));
		}
	}
	
	
	protected function _AfterDoEdit(){
		switch(strtolower(session('listaction'))){
			case "waitverify":
				$v = M('borrow_info')->field('borrow_uid,borrow_status,deal_time')->find(intval($_POST['id']));
				if(empty($v['deal_time'])){
					$newid = M('members')->where("id={$v['borrow_uid']}")->setInc('credit_use',floatval($_POST['borrow_money']));
					if($newid) M('borrow_info')->where("id={$v['borrow_uid']}")->setField('deal_time',time());
				}
				//$this->assign("waitSecond",1000);
				//Notice();s
			break;
		}
	}
	
	public function _listFilter($list){
		session('listaction',ACTION_NAME);
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
	 	$listType = $Bconfig['REPAYMENT_TYPE'];
	 	$BType = $Bconfig['BORROW_TYPE'];
		$row=array();
		$aUser = get_admin_name();
		foreach($list as $key=>$v){
			$v['repayment_type_num'] = $v['repayment_type'];
			$v['repayment_type'] = $listType[$v['repayment_type']];
			$v['borrow_type'] = $BType[$v['borrow_type']];
			if($v['deadline']) $v['overdue'] = getLeftTime($v['deadline']) * (-1);
			if($v['borrow_status']==1 || $v['borrow_status']==3 || $v['borrow_status']==5){
				$v['deal_uname_2'] = $aUser[$v['deal_user_2']];
				$v['deal_uname'] = $aUser[$v['deal_user']];
			}
			
			$row[$key]=$v;
		}
		return $row;
	}
	
	
	 public function doweek()
    {
		$map=array();
		$map['b.borrow_status'] = 6;
		if(!empty($_REQUEST['isShow'])){
			$week_1 = array(strtotime(date("Y-m-d",time())." 00:00:00"),strtotime("+7 day",strtotime(date("Y-m-d",time())." 23:59:59")));//一周内
			$map['b.deadline'] = array("between",$week_1);
		}
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid'] || $_REQUEST['uname']!=$_REQUEST['olduname']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['b.borrow_uid'] = $uid;
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		if( !empty($_REQUEST['uid'])&&!isset($search['uname']) ){
			$map['b.borrow_uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['b.borrow_uid'];
			$search['uname'] = $_REQUEST['uname'];
		}

		if(!empty($_REQUEST['bj']) && !empty($_REQUEST['money'])){
			$map['b.borrow_money'] = array($_REQUEST['bj'],$_REQUEST['money']);
			$search['bj'] = $_REQUEST['bj'];	
			$search['money'] = $_REQUEST['money'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['b.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['b.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		
		if(session('admin_is_kf')==1){
				$map['m.customer_id'] = session('admin_id');
		}else{
			if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
				$map['m.customer_id'] = $_REQUEST['customer_id'];
				$search['customer_id'] = $map['m.customer_id'];	
				$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
			}
			
			if($_REQUEST['customer_name'] && !$search['customer_id']){
				$cusname = urldecode($_REQUEST['customer_name']);
				$kfid = M('ausers')->getFieldByUserName($cusname,'id');
				$map['m.customer_id'] = $kfid;
				$search['customer_name'] = $cusname;	
				$search['customer_id'] = $kfid;	
			}
		}
		//分页处理
		import("ORG.Util.Page");
		$count = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理

		$field= 'b.id,b.borrow_name,b.borrow_uid,b.borrow_duration,b.borrow_type,b.borrow_money,b.borrow_fee,b.borrow_interest_rate,b.repayment_type,b.deadline,m.user_name,m.id mid';
		$list = M('borrow_info b')->field($field)->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		$list = $this->_listFilter($list);
		
        $this->assign("bj", array("gt"=>'大于',"eq"=>'等于',"lt"=>'小于'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
		$this->assign("xaction",ACTION_NAME);
        $this->assign("query", http_build_query($search));
		
        $this->display();
    }
	
	//swf上传图片
	public function swfUpload(){
		if($_POST['picpath']){
			$imgpath = substr($_POST['picpath'],1);
			if(in_array($imgpath,$_SESSION['imgfiles'])){
					 unlink(C("WEB_ROOT").$imgpath);
					 $thumb = get_thumb_pic($imgpath);
				$res = unlink(C("WEB_ROOT").$thumb);
				if($res) $this->success("删除成功","",$_POST['oid']);
				else $this->error("删除失败","",$_POST['oid']);
			}else{
				$this->error("图片不存在","",$_POST['oid']);
			}
		}else{
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Product/' ;
			$this->thumbMaxWidth = C('PRODUCT_UPLOAD_W');
			$this->thumbMaxHeight = C('PRODUCT_UPLOAD_H');
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$info = $this->CUpload();
			$data['product_thumb'] = $info[0]['savepath'].$info[0]['savename'];
			if(!isset($_SESSION['count_file'])) $_SESSION['count_file']=1;
			else $_SESSION['count_file']++;
			$_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];
			echo "{$_SESSION['count_file']}:".__ROOT__."/".$data['product_thumb'];//返回给前台显示缩略图
		}
	}

	//----- apply -----
	public function apply() {
		$map = array();
		import("ORG.Util.Page");
		$count = M('borrow_apply b')->join("{$this->pre}members m ON m.id=b.uid")->where($map)->count('b.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		
		$field= 'b.id,b.borrow_name,b.add_group,b.status,b.add_ip,b.add_time,m.id as uid,m.user_name as uname';
		$list = M('borrow_apply b')->field($field)->join("{$this->pre}members m ON m.id=b.uid")->where($map)->limit($Lsql)->order("b.id DESC")->select();
		
		$this->assign('list',$list);
		$this->assign('pagebar',$page);
        $this->display();
	}

	public function apply_edit() {
        $id = intval($_REQUEST['id']);
        $vo = D('borrow_apply')->find($id);
        if (empty($vo)) {
        	$this->error('数据不存在');
        }
        $vo['uname'] = m('members')->getFieldById($vo['uid'], 'user_name');
        $this->assign('vo', $vo);

		//资料列表
        $filelist = M('borrow_apply_file')->where(array('iid'=>$id))->select();
        $this->assign('filelist', $filelist);

		//审核日志
		$loglist = M('data_verify_log')->where(array('type'=>'borrowapply','did'=>$id))->order('op_time DESC')->select();
		$this->assign('loglist', $loglist);

		$this->display();
	}

	public function apply_borrow() {
		$id = intval($_GET['id']);
		$vo = D('borrow_apply')->find($id);
		if (empty($vo)) {
			exit('数据不存在');
		}
		
		$borrow_type = intval($_GET['borrow_type']);
		switch ($borrow_type) {
			case '1'://普通标
				break;
			case '2'://担保标
				break;
			case '3'://秒还标
				$this->assign("miao",'yes');
				break;
			case '4'://净值标
				break;
			case '5'://抵押标
				break;
		}

		$borrow_duration_day = explode("|",$this->glo['borrow_duration_day']);
		$day = range($borrow_duration_day[0],$borrow_duration_day[1]);
		$day_time = array();
		foreach ($day as $v) {
			$day_time[$v] = $v."天";
		}

		$borrow_duration = explode("|",$this->glo['borrow_duration']);
		$month = range($borrow_duration[0],$borrow_duration[1]);
		$month_time = array();
		foreach ($month as $v) {
			$month_time[$v] = $v."个月";
		}
		$rate_lixt = explode("|",$this->glo['rate_lixi']);
		$borrow_config = require C("APP_ROOT")."Conf/borrow_config.php";
		$this->assign("borrow_use",$borrow_config['BORROW_USE']);
		$this->assign("borrow_min",$borrow_config['BORROW_MIN']);
		$this->assign("borrow_max",$borrow_config['BORROW_MAX']);
		$this->assign("borrow_time",$borrow_config['BORROW_TIME']);
		$this->assign("borrow_type",$borrow_type);
		$this->assign("borrow_day_time",$day_time);
		$this->assign("borrow_month_time",$month_time);
		$this->assign("repayment_type",$borrow_config['REPAYMENT_TYPE']);
		$this->assign("rate_lixt",$rate_lixt);
		$this->assign("vo",$vo);
		
		$this->display();
	}

	private function _doApply_borrow($m) {
		$vo = M('borrow_apply')->find($m->id);
		$pre = C('DB_PREFIX');
		//相关的判断参数
		$rate_lixt = explode("|",$this->glo['rate_lixi']);
		$borrow_duration = explode("|",$this->glo['borrow_duration']);
		$borrow_duration_day = explode("|",$this->glo['borrow_duration_day']);
		$fee_borrow_manage = explode("|",$this->glo['fee_borrow_manage']);
		$vminfo = M('members m')->join("{$pre}member_info mf ON m.id=mf.uid")->field("m.user_leve,m.time_limit,mf.province_now,mf.city_now,mf.area_now")->where("m.id=".$vo['uid'])->find();
		//相关的判断参数
		$borrow['borrow_type'] = intval($_POST['borrow_type']);
		
		if(floatval($_POST['borrow_interest_rate'])>$rate_lixt[1] || floatval($_POST['borrow_interest_rate'])<$rate_lixt[0]) return array('status'=>false,'error'=>'提交的借款利率有误，请重试');
		$borrow['borrow_money'] = intval($_POST['borrow_money']);


		$_minfo = getMinfo($vo['uid'], true);
		$_capitalinfo = getMemberBorrowScan($vo['uid']);
		switch ($borrow['borrow_type']) {
			case 1://普通标
				if($_minfo['credit_cuse']<$borrow['borrow_money']) return array('status'=>false,'error'=>'您的可用信用额度为'.$_minfo['credit_cuse'].'元，小于您准备借款的金额，不能发标');
			break;
			case 2://担保标
				if($_minfo['borrow_vouch_cuse']<$borrow['borrow_money']) return array('status'=>false,'error'=>'您的可用信用担保借款额度为'.$_minfo['borrow_vouch_cuse'].'元，小于您准备借款的金额，不能发标');
			break;
			case 3://秒还标
			break;
			case 4://净值标
				///////////////////////////////////////////////////////
				$borrowNum = M('borrow_info')->field("borrow_type,count(id) as num,sum(borrow_money) as money,sum(repayment_money) as repayment_money")->where("borrow_uid = {$this->uid} AND borrow_status=6 ")->group("borrow_type")->select();
				$borrowDe = array();
				foreach ($borrowNum as $k => $v) {
					$borrowDe[$v['borrow_type']] = $v['money'] - $v['repayment_money'];
				}
				///////////////////////////////////////////////////
				$_netMoney = getFloatValue(0.9*$_minfo['money_collect']-$borrowDe[4],2);
				if($_netMoney<$borrow['borrow_money']) return array('status'=>false,'error'=>'您的净值额度'.$_netMoney.'元，小于您准备借款的金额，不能发标');
			break;
			case 5://抵押标
			break;
		}
		
		if ($borrow['borrow_type'] == 2) {//担保标
			$borrow['reward_vouch_rate'] = floatval($_POST['vouch_rate']);
			$borrow['reward_vouch_money'] = getFloatValue($borrow['borrow_money']*$borrow['reward_vouch_rate']/100,2);
			$borrow['vouch_member'] = text($_POST['vouch_member']);
		}
		
		$borrow['borrow_uid'] = $vo['uid'];
		$borrow['borrow_name'] = text($_POST['borrow_name_x']);
		$borrow['borrow_duration'] = ($borrow['borrow_type'] == 3)?1:intval($_POST['borrow_duration']);//秒标固定为一月
		$borrow['borrow_interest_rate'] = floatval($_POST['borrow_interest_rate']);
		if (strtolower($_POST['is_day']) == 'yes') $borrow['repayment_type'] = 1;
		elseif ($borrow['borrow_type'] == 3) $borrow['repayment_type'] = 2;//秒标按月还
		else $borrow['repayment_type'] = intval($_POST['repayment_type']);
		
		if ($_POST['show_tbzj'] == 1) $borrow['is_show_invest'] = 1;//共几期(分几次还)
		
		$borrow['total'] = ($borrow['repayment_type']==1)?1:$borrow['borrow_duration'];//共几期(分几次还)
		$borrow['borrow_status'] = 0;
		$borrow['borrow_use'] = intval($_POST['borrow_use']);
		$borrow['add_time'] = time();
		$borrow['collect_day'] = intval($_POST['borrow_time']);
		$borrow['add_ip'] = get_client_ip();
		$borrow['borrow_info'] = text($_POST['borrow_info_x']);
		$borrow['reward_type'] = intval($_POST['reward_type']);
		$borrow['reward_num'] = floatval($_POST["reward_type_{$borrow['reward_type']}_value"]);
		$borrow['borrow_min'] = intval($_POST['borrow_min']);
		$borrow['borrow_max'] = intval($_POST['borrow_max']);
		$borrow['province'] = $vminfo['province_now'];
		$borrow['city'] = $vminfo['city_now'];
		$borrow['area'] = $vminfo['area_now'];
		if ($_POST['is_pass'] && intval($_POST['is_pass']) == 1) $borrow['password'] = md5($_POST['password']);
		
		//借款费和利息
		$borrow['borrow_interest'] = getBorrowInterest($borrow['repayment_type'], $borrow['borrow_money'], $borrow['borrow_duration'], $borrow['borrow_interest_rate']);
		
		if ($borrow['repayment_type'] == 1) {//按天还
			$fee_rate = (is_numeric($fee_borrow_manage[0]))?($fee_borrow_manage[0]/100):0.001;
			$borrow['borrow_fee'] = getFloatValue($fee_rate*$borrow['borrow_money']*$borrow['borrow_duration'],2);
		} else {
			$fee_rate_1 = (is_numeric($fee_borrow_manage[1]))?($fee_borrow_manage[1]/100):0.02;
			$fee_rate_2=(is_numeric($fee_borrow_manage[2]))?($fee_borrow_manage[2]/100):0.002;
			if ($borrow['borrow_duration']>$fee_borrow_manage[3]&&is_numeric($fee_borrow_manage[3])) {
				$borrow['borrow_fee'] = getFloatValue($fee_rate_1*$borrow['borrow_money'],2);
				$borrow['borrow_fee'] += getFloatValue($fee_rate_2*$borrow['borrow_money']*($borrow['borrow_duration']-$fee_borrow_manage[3]),2);
			} else {
				$borrow['borrow_fee'] = getFloatValue($fee_rate_1*$borrow['borrow_money'],2);
			}
		}
		
		if ($borrow['borrow_type'] == 3) {//秒还标
			if ($borrow['reward_type']>0) {
				if ($borrow['reward_type'] == 1) $_reward_money = getFloatValue($borrow['borrow_money']*$borrow['reward_num']/100,2);
				elseif ($borrow['reward_type'] == 2) $_reward_money = getFloatValue($borrow['reward_num'],2);
			}
			$_reward_money = floatval($_reward_money);
			if (($_minfo['account_money']+$_minfo['back_money'])<($borrow['borrow_fee']+$_reward_money)) return array('status'=>false,'error'=>'发布此标您最少需保证您的帐户余额大于等于'.($borrow['borrow_fee']+$_reward_money).'元，以确保可以支付借款管理费和投标奖励费用');
		}
		
		return array('status'=>true,'data'=>$borrow);
	}

	public function doApply_edit() {
		//审核通过的无法再次操作审核
        $id = intval($_POST['id']);
        if (M('borrow_apply')->getFieldById($id, 'status') == 1) {
        	$this->error('该申请已经通过审核了');
        }

		$model = D('borrow_apply');
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        $model->deal_user = $this->admin_id;
        $model->deal_time = time();

        $msg = '';
        $model->startTrans();
        $status = true;

        if ($model->status == 1) {
        	$binfo = $this->_doApply_borrow($model);
			if ($binfo['status'] === false) {
				$status = false;
				$msg .= '借款发布失败，'.$binfo['error'].'<br />';
			}
        }

        if ($status) {
        	if ($result = $model->save()) { //保存成功
        		if (!empty($binfo['data'])) {
        			//审核通过后生成“初审待审核借款”
					$bid = M('borrow_info')->add($binfo['data']);
					if ($bid) {
						$pre = C('DB_PREFIX');
						if (!(m()->execute("update `{$pre}borrow_apply` set `bid`={$bid} WHERE `id`={$id}")&&m()->execute("update `{$pre}borrow_apply_file` set `bid`={$bid} WHERE `iid`={$id}"))) {
							$status = false;
						}
					} else {
						$status = false;
					}
        		}

	        	if ($status) {
	        		$vd = M('borrow_apply')->field("id,status,deal_info,deal_user")->find($id);
					//日志记录
					$lid = m('data_verify_log')->add(array(
						'type' 			=> 'borrowapply',
						'did' 			=> $vd['id'],
						'op_status' 	=> $vd['status'],
						'op_info' 		=> $vd['deal_info'],
						'op_uid' 		=> $vd['deal_user'],
						'op_time' 		=> time()
					));

					$status = $lid ? true : false;
	        	}

	        	if ($status) {
	        		$model->commit();
	        	} else {
	        		$model->rollback();
	        	}
	        }
        }

        if ($status) {
        	//成功提示
            $this->assign('jumpUrl', __URL__.'/apply.html');
            $msg .= '修改成功';
            $this->success(L($msg));
        } else {
        	//失败提示
            $this->assign('jumpUrl', __URL__.'/apply_edit?id='.$id);
            $msg .= '修改失败，请重试';
            $this->error(L($msg));
        }
	}

	public function apply_file() {
		$id = intval($_REQUEST['id']);
        $vo = D('borrow_apply_file')->find($id);
        if (empty($vo)) {
        	$this->error('数据不存在');
        }
        $this->assign('vo', $vo);

		//审核日志
		$loglist = M('data_verify_log')->where(array('type'=>'borrowapplyfile','did'=>$id))->order('op_time DESC')->select();
		$this->assign('loglist', $loglist);

		$this->display();
	}

	public function doApply_file() {
		$id = intval($_POST['id']);

		$model = D('borrow_apply_file');
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //处理文件
        if (!empty($_FILES['imgfile']['name'])) {
			$this->savePathNew = C("ADMIN_UPLOAD_DIR").'Borrow/'.$model->uid.'/';
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$info = $this->CUpload();
			$data['deal_image'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if ($data['deal_image']) $model->deal_image = $data['deal_image'];
		$model->deal_user = $this->admin_id;
		$model->deal_time = time();

        $msg = '';
        //保存当前数据对象
        if ($result = $model->save()) { //保存成功
			$vd = M('borrow_apply_file')->field("id,iid,status,deal_image,deal_info,deal_user")->find($id);
			//日志记录
			m('data_verify_log')->add(array(
				'type' 			=> 'borrowapplyfile',
				'did' 			=> $vd['id'],
				'op_status' 	=> $vd['status'],
				'op_image' 		=> $vd['deal_image'],
				'op_info' 		=> $vd['deal_info'],
				'op_uid' 		=> $vd['deal_user'],
				'op_time' 		=> time()
			));
            //成功提示
            $this->assign('jumpUrl', __URL__.'/apply_edit?id='.$vd['iid']);
            $msg .= '修改成功';
            $this->success(L($msg));
        } else {
            //失败提示
            $this->assign('jumpUrl', __URL__.'/apply_edit?id='.m('borrow_apply_file')->getFieldById($id, 'iidd'));
            $msg .= '修改失败，请重试';
            $this->error(L($msg));
        }
	}

	//swf上传图片
	public function apply_swfUpload() {
		if ($_POST['picpath']) {//删除
			$imgpath = substr($_POST['picpath'], 1);
			if (in_array($imgpath,$_SESSION['imgfiles'])) {
				unlink(C("WEB_ROOT").$imgpath);
				$thumb = get_thumb_pic($imgpath);
				$res = unlink(C("WEB_ROOT").$thumb);
				if($res) {
					//数据删除
					m("borrow_apply_file")->where("data_url='{$imgpath}'")->delete();
					$this->success("删除成功","",$_POST['oid']);
				} else {
					$this->error("删除失败","",$_POST['oid']);
				}
			} else {
				$this->error("图片不存在","",$_POST['oid']);
			}
		} else {//上传
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Borrow/A/';
			$this->thumbMaxWidth = C('BORROW_UPLOAD_W');
			$this->thumbMaxHeight = C('BORROW_UPLOAD_H');
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$info = $this->CUpload();

			$data['borrow_thumb'] = $info[0]['savepath'].$info[0]['savename'];

			//数据保存
			$infos = array(
				'uid' 		=> intval($_POST['uid']),
				'data_name' => $info[0]['name'],
				'data_url' 	=> $data['borrow_thumb'],
				'ext' 		=> $info[0]['extension'],
				'size' 		=> $info[0]['size'],
				'add_ip' 	=> get_client_ip(),
				'add_time' 	=> time()
			);
			if (!(M("borrow_apply_file")->add($infos))) {
				unlink(C("WEB_ROOT").$data['borrow_thumb']);
				unlink(C("WEB_ROOT").get_thumb_pic($data['borrow_thumb']));
				echo "error";
			} else {
				if(!isset($_SESSION['count_file'])) $_SESSION['count_file']=1;
				else $_SESSION['count_file']++;
				$_SESSION['imgfiles'][$_SESSION['count_file']] = $data['borrow_thumb'];
				echo "{$_SESSION['count_file']}:".__ROOT__.'/'.$data['borrow_thumb'];//返回给前台显示缩略图
			}
		}
	}

	public function apply_add() {
		$uid = intval($_REQUEST['uid']);
		$uname = m('members')->getFieldById($uid, 'user_name');
		if (empty($uname)) {
			$this->error('用户不存在');
		}
		$vo = array(
			'uid' 	=> $uid,
			'uname' => $uname
		);
		$this->assign('vo', $vo);
		$this->display();
	}

	public function doApply_add() {
		$uid = intval($_POST['uid']);

		$pre = c("DB_PREFIX");
		$status = true;
		$borrowApply = d('borrow_apply');
    	$borrowApply->startTrans();
    	//信息添加
    	$iid = M('borrow_apply')->add(array(
    		'uid' 			=> $uid,
    		'borrow_name' 	=> text($_POST['borrow_name']),
    		'borrow_info' 	=> text($_POST['borrow_info']),
    		'add_group' 	=> strtolower(GROUP_NAME),
    		'add_user' 		=> $this->admin_id,
    		'add_ip' 		=> get_client_ip(),
    		'add_time' 		=> time()
    	));
    	if ($iid) {
    		foreach ($_POST['swfimglist'] as $v) {
    			if (!(m()->execute("update `{$pre}borrow_apply_file` set `iid`={$iid} WHERE `data_url`='".substr($v, strlen(__ROOT__.'/'))."'"))) {
					$status = false;
					break;
				}
    		}
    	} else {
    		$status = false;
    	}
    	if ($status) {
    		$borrowApply->commit();
    		$this->assign('jumpUrl', __URL__.'/apply.html');
			$this->success('申请成功，请审核');
    	} else {
    		$borrowApply->rollback();
    		$this->assign('jumpUrl', __URL__.'/apply_add?uid='.$uid);
			$this->error('申请失败，请重试');
    	}
	}
	
}
?>