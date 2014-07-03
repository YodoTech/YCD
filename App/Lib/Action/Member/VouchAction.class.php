<?php
// 本类由系统自动生成，仅供测试用途
class VouchAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function summary(){
		$uid = $this->uid;
		$pre = C('DB_PREFIX');
		$vouching = M()->query("select count(id) as num,sum(vouch_money) as vouch_money from {$pre}borrow_vouch where uid={$uid} AND status=0");
		$vouchrepaymenting = M()->query("select count(id) as num,sum(vouch_money) as vouch_money from {$pre}borrow_vouch where uid={$uid} AND status=3");
		$vouchrepaymented = M()->query("select count(id) as num,sum(vouch_money) as vouch_money from {$pre}borrow_vouch where uid={$uid} AND status=1");
		$vouchbreak = M()->query("select count(id) as num,sum(vouch_money) as vouch_money from {$pre}borrow_vouch where uid={$uid} AND status=2");
		
		//$this->display("Public:_footer");
		$this->assign("vouching",$vouching[0]);
		$this->assign("vouchrepaymenting",$vouchrepaymenting[0]);
		$this->assign("vouchrepaymented",$vouchrepaymented[0]);
		$this->assign("vouchbreak",$vouchbreak[0]);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
	
	public function vouching(){
		$map['v.uid'] = $this->uid;
		$map['v.status'] = 0;
		
		$list = getMVouchList($map,15);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);

		$data['html'] = $this->fetch();
		//$this->display("Public:_footer");
		exit(json_encode($data));
	}

	public function vouchrepaymenting(){
		$map['v.uid'] = $this->uid;
		$map['v.status'] = 3;
		
		$list = getMVouchList($map,15);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		//$this->display("Public:_footer");

		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}

	public function vouchrepaymented(){
		$map['v.uid'] = $this->uid;
		$map['v.status'] = 1;
		
		$list = getMVouchList($map,15);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
		//$this->display("Public:_footer");
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}

    public function vouchfail(){
		$map['v.uid'] = $this->uid;
		$map['v.status'] = 2;
		
		$list = getMVouchList($map,15);
		$this->assign("list",$list['list']);
		$this->assign("pagebar",$list['page']);
	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }


}