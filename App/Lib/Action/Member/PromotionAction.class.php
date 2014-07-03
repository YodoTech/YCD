<?php
// 本类由系统自动生成，仅供测试用途
class PromotionAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function promotion(){
		$_P_fee=get_global_setting();
		$this->assign("reward",$_P_fee);	
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

    public function promotionlog(){
		$map['uid'] = $this->uid;
		$map['type'] = array("in","1,13");
		$list = getMoneyLog($map,15);
		
		$totalR = M('member_moneylog')->where("uid={$this->uid} AND type in(1,13)")->sum('affect_money');
		$this->assign("totalR",$totalR);		
		$this->assign("CR",M('members')->getFieldById($this->uid,'reward_money'));		
		$this->assign("list",$list['list']);		
		$this->assign("pagebar",$list['page']);		

		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }

	public function promotionfriend(){
		$pre = C('DB_PREFIX');
		$uid=session('u_id');
		$field = " m.id,m.user_name,m.reg_time,sum(ml.affect_money) jiangli ";
		$field1 = " m.user_name,m.reg_time";
		$vm = M("members m")->field($field)->join(" ".$pre."member_moneylog ml ON m.id = ml.target_uid ")->where(" m.recommend_id ={$uid} AND ml.type =13")->group("ml.target_uid")->select();
		$vm1 = M("members m")->field($field1)->where(" m.recommend_id ={$uid}")->group("m.id")->select();
		$this->assign("vm",$vm);	
		$this->assign("vi",$vm1);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
}