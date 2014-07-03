<?php
// 本类由系统自动生成，仅供测试用途
class VipAction extends MCommonAction {

    public function index(){
		$vo = M('members')->field('user_leve,time_limit')->find($this->uid);
		if($vo['user_leve']>0 && $vo['time_limit']>time()){
			$this->assign("vipTime",$vo['time_limit']);
		}
		$vx = M('vip_apply')->where("uid={$this->uid} AND status=0")->count("id");
		if($vx>0) $this->error("您的VIP申请已在处理中，请耐心等待！"); 
		$this->display();
    }
	
	public function getkf(){
		$province = intval($_POST['province_nowp']);
		$city = intval($_POST['city_now']);
		$area = intval($_POST['area_now']);
		
		$map['is_kf'] = 1;
		$map['area_id'] = $area;
		$count = M('ausers')->where($map)->count('id');
		if($count==0){
			$map['area_id'] = $city;
			$count = M('ausers')->where("area_id={$city}")->count('id');
		}
		if($count==0){
			$map['area_id'] = $province;
			$count = M('ausers')->where("area_id={$province}")->count('id');
		}
		if($count==0) unset($map['area_id']);		
		
		//分页处理
		import("ORG.Util.Page");
		$count = M('ausers')->where($map)->count('id');
		$p = new Page($count, $size);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$list = M('ausers')->where($map)->limit($Lsql)->select();
		
		$save['province_now'] = $province;
		$save['city_now'] = $city;
		$save['area_now'] = $area;
		$newid = M('member_info')->where("uid={$this->uid}")->save($save);
		
		$this->assign("list",$list);
		$this->assign("count",$count);
		$this->assign("page",$page);
		$data['html'] = $this->fetch();
		ajaxmsg($data);
	}

	public function apply(){
		$province = intval($_POST['province_now']);
		$city = intval($_POST['city_now']);
		$area = intval($_POST['area_now']);
		$kfid = intval($_POST['kfid']);
		$des = text($_POST['des']);
		
		$savedata['province_now'] = $province;
		$savedata['city_now'] = $city;
		$savedata['area_now'] = $area;
		$savedata['kfid'] = $kfid;
		$savedata['uid'] = $this->uid;
		$savedata['des'] = $des;
		$savedata['add_time'] = time();
		$savedata['status'] = 0;
	
		$newid = M('vip_apply')->add($savedata);
		if($newid) ajaxmsg();
		else ajaxmsg("保存失败，请重试",0);
	}

	
	public function getarea(){
		$rid = intval($_GET['rid']);
		if(empty($rid)){
			$data['NoCity'] = 1;
			exit(json_encode($data));
		}
		$map['reid'] = $rid;
		$alist = M('area')->field('id,name')->order('sort_order DESC')->where($map)->select();

		if(count($alist)===0){
			$str="<option value=''>--该地区下无下级地区--</option>\r\n";
		}else{
			if($rid==1) $str.="<option value='0'>请选择省份</option>\r\n";
			foreach($alist as $v){
				$str.="<option value='{$v['id']}'>{$v['name']}</option>\r\n";
			}
		}
		$data['option'] = $str;
		$res = json_encode($data);
		echo $res;
	}	

}