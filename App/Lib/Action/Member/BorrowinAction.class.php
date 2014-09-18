<?php
// 本类由系统自动生成，仅供测试用途
class BorrowinAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function summary(){
		$pre = C('DB_PREFIX');
		
		$this->assign("mx",getMemberBorrowScan($this->uid));
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	
	public function borrowing(){
		$map['borrow_uid'] = $this->uid;
		$map['borrow_status'] = array("in","0,2");
		
		if($_GET['start_time2']&&$_GET['end_time2']){
			$_GET['start_time2'] = strtotime($_GET['start_time2']." 00:00:00");
			$_GET['end_time2'] = strtotime($_GET['end_time2']." 23:59:59");
			
			if($_GET['start_time2']<$_GET['end_time2']){
				$map['add_time']=array("between","{$_GET['start_time2']},{$_GET['end_time2']}");
				$search['start_time2'] = $_GET['start_time2'];
				$search['end_time2'] = $_GET['end_time2'];
			}
		}
		
		$list = getBorrowList($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}

	public function borrowpaying(){
		$map['borrow_uid'] = $this->uid;
		$map['borrow_status'] = 6;
		
		if($_GET['start_time']&&$_GET['end_time']){
			$_GET['start_time'] = strtotime($_GET['start_time']." 00:00:00");
			$_GET['end_time'] = strtotime($_GET['end_time']." 23:59:59");
			
			if($_GET['start_time']<$_GET['end_time']){
				$map['add_time']=array("between","{$_GET['start_time']},{$_GET['end_time']}");
				$search['start_time'] = $_GET['start_time'];
				$search['end_time'] = $_GET['end_time'];
			}
		}
		$map['status'] = 7;
		$list = getBorrowList($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}


	public function borrowbreak(){
		$Wsql="";
		if($_GET['start_time1']&&$_GET['end_time1']){
			$_GET['start_time1'] = strtotime($_GET['start_time1']." 00:00:00");
			$_GET['end_time1'] = strtotime($_GET['end_time1']." 23:59:59");
			
			if($_GET['start_time1']<$_GET['end_time1']){
				$Wsql = " AND ( d.deadline between {$_GET['start_time1']} AND {$_GET['end_time1']} ) ";
				$search['start_time1'] = $_GET['start_time1'];
				$search['end_time1'] = $_GET['end_time1'];
			}
		}
		
		$list = getMBreakRepaymentList($this->uid,10,$Wsql);
		
		//print_r($list['list']);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}
	
	public function borrowfail(){
		$map['borrow_uid'] = $this->uid;
		$map['borrow_status'] = 3;
		
		if($_GET['start_time4']&&$_GET['end_time4']){
			$_GET['start_time4'] = strtotime($_GET['start_time4']." 00:00:00");
			$_GET['end_time4'] = strtotime($_GET['end_time4']." 23:59:59");
			
			if($_GET['start_time4']<$_GET['end_time4']){
				$map['add_time']=array("between","{$_GET['start_time4']},{$_GET['end_time4']}");
				$search['start_time4'] = $_GET['start_time4'];
				$search['end_time4'] = $_GET['end_time4'];
			}
		}
		
		$list = getBorrowList($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}

	
	public function borrowfail2(){
		$map['borrow_uid'] = $this->uid;
		$map['borrow_status'] = 5;
		
		if($_GET['start_time5']&&$_GET['end_time5']){
			$_GET['start_time5'] = strtotime($_GET['start_time5']." 00:00:00");
			$_GET['end_time5'] = strtotime($_GET['end_time5']." 23:59:59");
			
			if($_GET['start_time5']<$_GET['end_time5']){
				$map['add_time']=array("between","{$_GET['start_time5']},{$_GET['end_time5']}");
				$search['start_time5'] = $_GET['start_time5'];
				$search['end_time5'] = $_GET['end_time5'];
			}
		}
		
		$list = getBorrowList($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}

	
	public function borrowfail1(){
		$map['borrow_uid'] = $this->uid;
		$map['borrow_status'] = 1;
		
		if($_GET['start_time6']&&$_GET['end_time6']){
			$_GET['start_time6'] = strtotime($_GET['start_time6']." 00:00:00");
			$_GET['end_time6'] = strtotime($_GET['end_time6']." 23:59:59");
			
			if($_GET['start_time6']<$_GET['end_time6']){
				$map['add_time']=array("between","{$_GET['start_time6']},{$_GET['end_time6']}");
				$search['start_time6'] = $_GET['start_time6'];
				$search['end_time6'] = $_GET['end_time6'];
			}
		}
		
		$list = getBorrowList($map,10);
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}


	public function borrowdone(){
		$map['borrow_uid'] = $this->uid;
		$map['borrow_status'] = 7;
		
		if($_GET['start_time8']&&$_GET['end_time8']){
			$_GET['start_time8'] = strtotime($_GET['start_time8']." 00:00:00");
			$_GET['end_time8'] = strtotime($_GET['end_time8']." 23:59:59");
			
			if($_GET['start_time8']<$_GET['end_time8']){
				$map['add_time']=array("between","{$_GET['start_time8']},{$_GET['end_time8']}");
				$search['start_time8'] = $_GET['start_time8'];
				$search['end_time8'] = $_GET['end_time8'];
			}
		}
		
		$list = getBorrowList($map,10);
		
		$this->assign('search',$search);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}

	public function cancel(){ajaxmsg("操作不允许");//停用
		$id = intval($_POST['id']);
		$newid = M('borrow_info')->where("borrow_uid={$this->uid} AND id={$id} AND borrow_status=0")->delete();
		if($newid) ajaxmsg("撤消成功");
		else ajaxmsg("出错，如果您正在撤回的是还未初审的标，请重试，如已经初审，则不能撤回",0);
			
	}
	
	public function doexpired(){
		$borrow_id = intval($_POST['bid']);
		$sort_order = intval($_POST['sort_order']);
		$newid = borrowRepayment($borrow_id,$sort_order);
		if($newid===true) ajaxmsg();
		elseif($newid===false) ajaxmsg('还款失败，请重试',0);
		else ajaxmsg($newid,0);
	}

}