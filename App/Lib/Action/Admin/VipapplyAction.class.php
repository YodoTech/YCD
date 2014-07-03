<?php
// 全局设置
class VipapplyAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$map=array();
		if($_REQUEST['uid'] && $_REQUEST['uname']){
			$map['v.uid'] = $_REQUEST['uid'];
			$search['uid'] = $map['v.uid'];	
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['uname'] && !$search['uid']){
			$map['m.user_name'] = array("like",urldecode($_REQUEST['uname'])."%");
			$search['uname'] = urldecode($_REQUEST['uname']);	
		}
		
		if($_REQUEST['realname']){
			$map['mi.real_name'] = urldecode($_REQUEST['realname']);
			$search['real_name'] = $map['mi.real_name'];	
		}
		
		if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
			$map['v.kfid'] = $_REQUEST['customer_id'];
			$search['customer_id'] = $map['v.kfid'];	
			$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
		}
		
		if($_REQUEST['customer_name'] && !$search['customer_id']){
			$cusname = urldecode($_REQUEST['customer_name']);
			$kfid = M('ausers')->getFieldByUserName($cusname,'id');
			$map['v.kfid'] = $kfid;
			$search['customer_name'] = $cusname;	
			$search['customer_id'] = $kfid;	
		}
		
		if(isset($_REQUEST['status'])&&$_REQUEST['status']!=''){
			$map['v.status'] = $_REQUEST['status'];
			$search['status'] = $map['v.status'];	
		}

		if(!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])){
			$timespan = strtotime(urldecode($_REQUEST['start_time'])).",".strtotime(urldecode($_REQUEST['end_time']));
			$map['v.add_time'] = array("between",$timespan);
			$search['start_time'] = urldecode($_REQUEST['start_time']);	
			$search['end_time'] = urldecode($_REQUEST['end_time']);	
		}elseif(!empty($_REQUEST['start_time'])){
			$xtime = strtotime(urldecode($_REQUEST['start_time']));
			$map['v.add_time'] = array("gt",$xtime);
			$search['start_time'] = $xtime;	
		}elseif(!empty($_REQUEST['end_time'])){
			$xtime = strtotime(urldecode($_REQUEST['end_time']));
			$map['v.add_time'] = array("lt",$xtime);
			$search['end_time'] = $xtime;	
		}
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		
		//分页处理
		import("ORG.Util.Page");
		$count = M('vip_apply v')->join("{$this->pre}members m ON v.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->count('m.id');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		
		$field= 'v.*,m.user_name as uname,mi.real_name';
		$list = M('vip_apply v')->field($field)->join("{$this->pre}members m ON v.uid=m.id")->join("{$this->pre}member_info mi ON mi.uid=m.id")->where($map)->limit($Lsql)->order('v.id DESC')->select();
		$list = $this->_listFilter($list);

        $this->assign("status", array('待审核','已通过审核','未通过审核'));
        $this->assign("list", $list);
        $this->assign("pagebar", $page);
        $this->assign("search", $search);
        $this->display();
    }
	public function edit(){
		setBackUrl();
		$id=intval($_GET['id']);
		$aUser = get_admin_name();
		$vo = M('vip_apply')->find($id);
		if($vo['status']!=0) $this->error("审核过的不能再次审核");
		$vo['kfuname'] = $aUser[$vo['kfid']];
		$vo['uname'] = M('members')->getFieldById($vo['uid'],'user_name');
		$this->assign("vo",$vo);
		$this->display();
	}

	public function doEdit(){
        $model = D(ucfirst($this->getActionName()));
        if (false === $model->create()) {
            $this->error($model->getError());
        }	
			$model->deal_time = time();
			$model->deal_user = session('admin_id');
        //保存当前数据对象
        if ($result = $model->save()) { //保存成功
			
			if($_POST['status']==1){
				$vx = M('vip_apply')->field("uid,kfid")->find(intval($_POST['id']));
				$uid = $vx['uid'];
				$datag = get_global_setting();
				$aUser = get_admin_name();
				$newx = memberMoneyLog($uid,14,-$datag['fee_vip'],"升级VIP成功");
				memberLimitLog($uid,11,$this->glo['limit_vip'],"VIP审核通过");
				addInnerMsg($uid,"您的VIP申请审核已通过","您的VIP申请审核已通过");
				$vo = M("members")->field("user_phone,user_name,recommend_id")->where("id = {$uid}")->find();
				
				SMStip("vip",$vo['user_phone'],array("#USERANEM#"),array($vo['user_name']));
				if($newx){
					$vmo = M('members')->field("user_leve,time_limit")->find($vx['uid']);
					$savex['customer_id'] = $vx['kfid'];
					$savex['customer_name'] = $aUser[$vx['kfid']];
					$savex['user_leve'] = 1;
					if($vmo['time_limit']>time()) $savex['time_limit'] = strtotime("+1 year",$vmo['time_limit']);
					else $savex['time_limit'] = strtotime("+1 year");
					M('members')->where("id={$uid}")->save($savex);
				}
			}else{
				addInnerMsg($uid,"您的VIP申请审核未通过","您的VIP申请审核未通过");
			}
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
            //失败提示
            $this->error(L('修改失败'));
        }
	}

	
	public function _listFilter($list){
		$row=array();
		$aUser = get_admin_name();
		foreach($list as $key=>$v){
			$v['a_kfName'] = $aUser[$v['kfid']];
			$row[$key]=$v;
		}
		return $row;
	}
	
	public function getusername(){
		$uname = M("members")->getFieldById(intval($_POST['uid']),"user_name");
		if($uname) exit(json_encode(array("uname"=>"<span style='color:green'>".$uname."</span>")));
		else exit(json_encode(array("uname"=>"<span style='color:orange'>不存在此会员</span>")));
	}
	
}
?>