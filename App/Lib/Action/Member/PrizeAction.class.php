<?php
// 本类由系统自动生成，仅供测试用途
class PrizeAction extends MCommonAction {

    public function index(){
		$this->display();
    }

    public function log(){
		$map['uid'] = $this->uid;
		//分页处理
		import("ORG.Util.Page");
		$count = M('prize_log')->where($map)->count('id');
		$p = new Page($count, 10);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$list = M('prize_log')->where($map)->order('add_time DESC')->limit($Lsql)->select();
		
		$this->assign("list",$list);
		$this->assign("pagebar",$page);
		$data['html'] = $this->fetch();
		exit(json_encode($data));
    }
}