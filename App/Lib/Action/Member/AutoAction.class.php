<?php
// 本类由系统自动生成，仅供测试用途
class AutoAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function auto(){
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

    public function autolong(){
	
		$vo = M('auto_borrow')->where("uid={$this->uid} AND borrow_type=1")->find();
		$vo['is_use_name'] = ($vo['is_use']==0)?"当前设置已暂停使用":"当前设置已启用";
		$x = M('members')->field("time_limit,user_leve")->find($this->uid);
		if($x['time_limit']>0 && $x['user_leve']==1) $is_vip=1;
		else $is_vip=0;
		
		$this->assign('isvip',$is_vip);
		$this->assign('vo',$vo);

		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	
	public function setupauto(){
		$aid = intval($_POST['aid']);
		$s = intval($_POST['s']);
		$vo = M('auto_borrow')->where("uid={$this->uid} AND id={$aid}")->find();
		if(is_array($vo)){
			$newid = M('auto_borrow')->where("id={$aid}")->setField('is_use',$s);
			if($newid) exit("1");
			else exit("0");
		}else{
			exit("0");
		}
	}
	
	public function savelong(){
		$x = M('members')->field("time_limit,user_leve")->find($this->uid);
		($x['time_limit']>0 && $x['user_leve']==1)?$is_vip=1:$is_vip=0;
		(intval($_POST['tendAmount'])==0 && $is_vip==1)?$is_full=1:$is_full=0;
		
		$duration = explode(",",text($_POST['loancycle']));
		$data['uid'] = $this->uid;
		$data['account_money'] = intval($_POST['miniamount']);
		$data['borrow_type'] = intval($_POST['borrowtype']);
		$data['interest_rate'] = intval($_POST['interest']);
		$data['duration_from'] = intval($duration[0]);
		$data['end_time'] = strtotime($_POST['expireddate']." 00:00:00");
		$data['duration_to'] = intval($duration[1]);
		$data['is_auto_full'] = $is_full;
		$data['invest_money'] = intval($_POST['tendAmount']);
		$data['add_ip'] = get_client_ip();
		$data['add_time'] = time();
		
		$c = M('auto_borrow')->field('id')->where("uid={$this->uid} AND borrow_type={$data['borrow_type']}")->find();
		if(is_array($c)){
			$data['id'] = $c['id'];
			$newid = M('auto_borrow')->save($data);
			if($newid) ajaxmsg("修改成功",1);
			else ajaxmsg("修改失败，请重试",0);
		}
		else{
			$newid = M('auto_borrow')->add($data);
			if($newid) ajaxmsg("添加成功",1);
			else ajaxmsg("添加失败，请重试",0);
		}
	}


    public function autoshort(){
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

    public function autotransferindex(){
	
		$vo = M('auto_borrow')->where("uid={$this->uid} AND borrow_type=3")->find();
		$vo['is_use_name'] = ($vo['is_use']==0)?"当前设置已暂停使用":"当前设置已启用";
		$x = M('members')->field("time_limit,user_leve")->find($this->uid);
		if($x['time_limit']>0 && $x['user_leve']==1) $is_vip=1;
		else $is_vip=0;
		
		$this->assign('isvip',$is_vip);
		$this->assign('vo',$vo);
		$this->display();
    }

}