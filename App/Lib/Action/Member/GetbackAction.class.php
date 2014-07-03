<?php
// 本类由系统自动生成，仅供测试用途
class GetbackAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function summary(){
		$pre = C('DB_PREFIX');
		$map['d.status'] = 1;
		$map['d.investor_uid'] = $this->uid;
		
		
		$sum = M('investor_detail')->field("count(id) as num,total")->where("invest_id={$v['id']} AND status=1")->find();
		
		$time_1 = strtotime(date("Y-m-d",time())." 00:00:00").",".strtotime("+1 month",strtotime(date("Y-m-d",time())." 23:59:59"));
		$map_1['deadline'] = array("between","{$time_1}");
		$map_1m = array_merge($map,$map_1);
		$listmonth_1 = getBackingList($map_1m,15);
		
		
		$time_3 = strtotime(date("Y-m-d",time())." 00:00:00").",".strtotime("+3 month",strtotime(date("Y-m-d",time())." 23:59:59"));
		$map_3['deadline'] = array("between","{$time_3}");
		$map_3m = array_merge($map,$map_3);
		$listmonth_3 = getBackingList($map_3m,15);
		
		
		$time_6 = strtotime(date("Y-m-d",time())." 00:00:00").",".strtotime("+6 month",strtotime(date("Y-m-d",time())." 23:59:59"));
		$map_6['deadline'] = array("between","{$time_6}");
		$map_6m = array_merge($map,$map_6);
		$listmonth_6 = getBackingList($map_6m,15);

		//$this->display("Public:_footer");
		if($_GET['type']==1){
			$start = strtotime($_GET['start_time']." 00:00:00");
			$end = strtotime($_GET['end_time']." 23:59:59");
			$search_map['start_time'] = $_GET['start_time'];
			$search_map['end_time'] = $_GET['end_time'];
			$map['d.deadline'] = array("between","{$start},{$end}");
			$this->assign("search",$search_map);
		}elseif($_GET['type']==2){
			if($_GET['long']!="all"){
				$long=intval($_GET['long']);
				$start = strtotime(date("Y-m-d",time())." 00:00:00");
				$end = strtotime("+{$long} month",strtotime(date("Y-m-d",time())." 23:59:59"));
				$map['d.deadline'] = array("between","{$start},{$end}");
			}
		}
		$list = getBackingList($map,15);
		//$this->display("Public:_footer");
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		$this->assign("total",$list['total_money']);
		$this->assign("num",$list['total_num']);
		$this->assign("total_1",$listmonth_1['month_total']);
		$this->assign("total_3",$listmonth_3['month_total']);
		$this->assign("total_6",$listmonth_6['month_total']);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
}