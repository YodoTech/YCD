<?php
// 本类由系统自动生成，仅供测试用途
class ToolAction extends HCommonAction {
	//圈子首页
    public function index(){
		if($_POST){
			$money = floatval($_POST['money']);
			$rate = floatval($_POST['interest_rate']);
			$month = intval($_POST['month']);
			
			$total = ($rate/12 * $month * $money)/100 + $money;
			$fee = getFloatValue( $total/$month,4);
			
			$monthData['money'] = $money;
			$monthData['year_apr'] = $rate;
			$monthData['duration'] = $month;
			$repay_list = EqualMonth($monthData);
			$this->assign('repay_list',$repay_list);

			$monthData['type'] = "all";
			$repay_detail = EqualMonth($monthData);
			$this->assign('repay_detail',$repay_detail);
			$this->assign('m',$month);
			
			$data['html_1'] = $this->fetch('index_res_1');
			$data['html_2'] = $this->fetch('index_res_2');
			exit(json_encode($data));
		}

		$this->display();
	}
    public function tool2(){
		if($_POST){
			$money = floatval($_POST['money']);
			$rate = floatval($_POST['interest_rate']);
			$month = ($_POST['selDays']== -1)?intval($_POST['month']):intval($_POST['selDays']);
			$fee = getFloatValue( ($rate/12 * $month * $money)/100 + $money,4);
			$this->assign('m',$month);
			$this->assign('fee',$fee);
			$data['html'] = $this->fetch('tool2_res');
			exit(json_encode($data));
		}
		$this->display();
	}
    public function tool3(){
		if($_POST){
			$money = floatval($_POST['money']);
			$rate = floatval($_POST['interest_rate']);
			$month = ($_POST['selDays']== -1)?intval($_POST['month']):intval($_POST['selDays']);
			$fee = getFloatValue( ($rate/12 * $month * $money)/100 + $money,4);
			$this->assign('mmoney',$money);
			$this->assign('m',$month);
			$this->assign('fee',$fee);

			$parm['month_times'] = $month;
			$parm['account'] = $money;
			$parm['year_apr'] = $rate;
			$repay_detail = EqualSeason($parm);
			$parm['type'] = "all";
			$repay_summary = EqualSeason($parm);
			
			$this->assign('repay_detail',$repay_detail);
			$this->assign('repay_summary',$repay_summary);

			$data['html_1'] = $this->fetch('tool3_res_1');
			$data['html_2'] = $this->fetch('tool3_res_2');
			exit(json_encode($data));
		}
		$this->display();
	}
	///////////////////////////////////实时财务开始 2013-05-11////////////////////////////////
	
	public function finanz(){
		$this->assign( "mborrowOut",M( "borrow_info" )->where( "borrow_status in(6,7,8,9)" )->sum( "borrow_money" ) );
		$this->assign( "mborrowOutNum",M( "borrow_info" )->where( "borrow_status in(6,7,8,9)" )->count( "id" ) );
		$this->assign( "mborrowBail",1000000-M("member_moneylog")->where(" type=23 ")->sum("affect_money"));
		$this->assign( "mborrowLimit",M( "borrow_info" )->where( "borrow_status in(6,8,9)" )->sum( "borrow_money" ) );

		$this->display();
	}

	public function finanzData(){
		$per = C('DB_PREFIX');
		
		$site = intval($_GET['site']);
		switch ($site) {
			case 2:
				$map = ' d.repayment_time=0 AND d.deadline<'.time().' AND d.status in(3,5,7) ';
				$field = "sum(d.capital)+sum(d.interest) as capital_all,d.borrow_uid,m.user_name,d.borrow_id,b.borrow_name,sum(d.interest) as interest,d.repayment_time,d.deadline";
				
				//分页处理
				import("ORG.Util.Page");
				$xcount = M('investor_detail d')->field("d.id")->where($map)->group('d.sort_order,d.borrow_id')->buildSql();
				$newxsql = M()->query("select count(*) as tc from {$xcount} as t");
				$count = $newxsql[0]['tc'];
				$p = new Page($count, 10);
				$page = $p->show();
				$Lsql = "{$p->firstRow},{$p->listRows}";
				//分页处理
				
				$lately = M('investor_detail d')->field($field)
					->join("{$per}members m ON m.id=d.borrow_uid")
					->join("{$per}borrow_info b ON b.id=d.borrow_id")
					->where($map)->group('d.sort_order,d.borrow_id')
					->order("d.deadline asc")
					->limit($Lsql)
					->select();

				$this->assign("page", $page);
				$this->assign("tendlately", $lately);

				$data['html'] = $this->fetch('finanz_res1');
				exit(json_encode($data));
				break;
			case 3:
				$map = ' b.status in(4,5) AND b.investor_uid <> 1';
				// $field = " b.investor_uid,sum((b.deadline-b.add_time)*investor_capital/3600/24/30) as money_all,b.investor_uid,m.user_name ";
				$field = " b.investor_uid,sum(investor_capital) as money_all,b.investor_uid,m.user_name ";
				
				//分页处理
				import("ORG.Util.Page");
				$xcount = M('borrow_investor b')->field("b.investor_uid")->where($map)->group('b.investor_uid')->buildSql();
				$newxsql = M()->query("select count(*) as tc from {$xcount} as t");
				$count = $newxsql[0]['tc'];
				$p = new Page($count, 10);
				$page = $p->show();
				$Lsql = "{$p->firstRow},{$p->listRows}";
				//分页处理
				
				$lately = M('borrow_investor b')->field($field)
					->join("{$per}members m ON m.id=b.investor_uid")
					->where($map)->group('b.investor_uid')
					->order("money_all desc")
					->limit($Lsql)->select();

				foreach ($lately as $k => $v) {
					$lately[$k]['money_all'] = Fmoney($v['money_all']);
				}
				
				$this->assign("page", $page);
				$this->assign("tenddata", $lately);
				$data['html'] = $this->fetch('finanz_res2');
				exit(json_encode($data));
				break;
			case 4:
				$map = ' b.status in(4,5) AND b.borrow_uid <> 1';
				// $field = " b.borrow_uid,sum((b.deadline-b.add_time)*investor_capital/3600/24/30) as money_all,b.borrow_uid,m.user_name ";
				$field = " b.borrow_uid,sum(investor_capital) as money_all,b.borrow_uid,m.user_name ";
				
				//分页处理
				import("ORG.Util.Page");
				$xcount = M('borrow_investor b')->field("b.investor_uid")->where($map)->group('b.investor_uid')->buildSql();
				$newxsql = M()->query("select count(*) as tc from {$xcount} as t");
				$count = $newxsql[0]['tc'];
				$p = new Page($count, 10);
				$page = $p->show();
				$Lsql = "{$p->firstRow},{$p->listRows}";
				//分页处理
				
				$lately = M('borrow_investor b')->field($field)
					->join("{$per}members m ON m.id=b.borrow_uid")
					->where($map)->group('b.borrow_uid')
					->order("money_all desc")
					->limit($Lsql)->select();

				foreach ($lately as $k => $v) {
					$lately[$k]['money_all'] = Fmoney($v['money_all']);
				}
				
				$this->assign("page", $page);
				$this->assign("tenddata", $lately);
				$data['html'] = $this->fetch('finanz_res2');
				exit(json_encode($data));
				break;
			case 1:
			default:
				$time = strtotime(date("Y-m-d"));
				$map = ' d.deadline> '.$time.' AND d.deadline< '.($time+3600*24*7).' AND d.status in(1,2,7) ';
				$field = "sum(d.capital)+sum(d.interest) as capital_all,d.borrow_uid,m.user_name,d.borrow_id,b.borrow_name,sum(d.interest) as interest,d.repayment_time,d.deadline";
				
				//分页处理
				import("ORG.Util.Page");
				$xcount = M('investor_detail d')->field("d.id")->where($map)->group('d.sort_order,d.borrow_id')->buildSql();
				$newxsql = M()->query("select count(*) as tc from {$xcount} as t");
				$count = $newxsql[0]['tc'];
				$p = new Page($count, 10);
				$page = $p->show();
				$Lsql = "{$p->firstRow},{$p->listRows}";
				//分页处理
				
				$lately = M('investor_detail d')->field($field)
					->join("{$per}members m ON m.id=d.borrow_uid")
					->join("{$per}borrow_info b ON b.id=d.borrow_id")
					->where($map)->group('d.sort_order,d.borrow_id')
					->order("d.deadline asc")
					->limit($Lsql)
					->select();

				$this->assign("page", $page);
				$this->assign("tendlately", $lately);

				$data['html'] = $this->fetch('finanz_res1');
				exit(json_encode($data));
				break;
		}

		$data['html'] = "无相关记录";
		exit(json_encode($data));
	}
	///////////////////////////////////实时财务开始 2013-05-11////////////////////////////////
	
	
	
	
}