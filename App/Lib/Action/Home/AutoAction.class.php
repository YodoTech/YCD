<?php
// 本类由系统自动生成，仅供测试用途
class AutoAction extends HCommonAction {
	private $updir = NULL;
	public function _MyInit(){
		$this->updir = dirname(C("WEB_ROOT"))."/AutoDo/";
	}
	public function index(){
		$key = $_GET['key'];
		$arg = file_get_contents($this->updir."config.txt");
		$arga = explode("|",$arg);
		$rate = intval($arga[1]);
		if($key!=$arga[2]) exit("fail|密钥错误");
		$total = 0;
		if($rate>0){
			$list = M("member_money")->field("uid,account_money")->where("account_money>0")->select();
			$i = 0;
			foreach($list as $v){
				$amoney = getFloatValue(($v['account_money']*$rate/10000),2);
				$re = memberMoneyLog($v['uid'],32,$amoney,date("Y年m月d日")."利息");
				if($re){
					$i++;
					$total+=$amoney;
				}
			}
			$all = count($list);
			$str = "共{$all}人需要发布利息，共成功对{$i}人发放共计{$total}元利息";
			echo "success|{$str}";
		}else{
			echo "success|利率为0，不返利";
		}
		echo "\r\n".date("Y-m-d H:i:s",time());
		exit();
	}
	public function continueAuto(){

	}
}