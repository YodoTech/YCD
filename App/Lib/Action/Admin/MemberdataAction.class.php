<?php
// 全局设置
class MemberdataAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$map=array();
		if($_REQUEST['se_type']!=""){
			$map['mf.type'] = intval($_REQUEST['se_type']);
			$search['type'] = $map['mf.type'];
		}
		if($_REQUEST['status']!=""){
			$map['mf.status'] = intval($_REQUEST['status']);
		}
		if(!empty($_REQUEST['uname'])&&!$_REQUEST['uid']){
			$uid = M("members")->getFieldByUserName(text($_REQUEST['uname']),'id');
			$map['mf.uid'] = $uid;
			$search['uname'] = $_REQUEST['uname'];
		}
		if(!empty($_REQUEST['uid'])){
			$map['mf.uid'] = intval($_REQUEST['uid']);
			$search['uid'] = $map['mf.uid'];
			$search['uname'] = $_REQUEST['uname'];
		}
		
		if($_REQUEST['customer_id'] && $_REQUEST['customer_name']){
			$map['m.customer_id'] = $_REQUEST['customer_id'];
			$search['customer_id'] = $map['m.uid'];	
			$search['customer_name'] = urldecode($_REQUEST['customer_name']);	
		}
		
		if($_REQUEST['customer_name'] && !$search['customer_id']){
			$cusname = urldecode($_REQUEST['customer_name']);
			$kfid = M('ausers')->getFieldByUserName($cusname,'id');
			$map['m.customer_id'] = $kfid;
			$search['customer_name'] = $cusname;	
			$search['customer_id'] = $kfid;	
		}
		
		if(session('admin_is_kf')==1)	$map['m.customer_id'] = session('admin_id');
		
		import("ORG.Util.Page");
		$count = M('member_data_info mf')->join("{$this->pre}members m ON m.id=mf.uid")->where($map)->count('mf.uid');
		$p = new Page($count, C('ADMIN_PAGE_SIZE'));
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		$list = M('member_data_info mf')->field('mf.*,m.user_name as uname,m.customer_name')->join("{$this->pre}members m ON m.id=mf.uid")->where($map)->limit($Lsql)->order("mf.id DESC")->select();
		$list = $this->_listFilter($list);
		
		$this->assign('search',$search);
		$this->assign('list',$list);
		$this->assign('pagebar',$page);
        $this->display();
    }
	public function _editFilter($id){
		setBackUrl();

		//审核日志
		$loglist = M('data_verify_log')->where(array('type'=>'memberdata','did'=>$id))->order('op_time DESC')->select();
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		foreach($loglist as $k=>$v) {
			$loglist[$k]['op_status_name'] = $Bconfig['DATA_STATUS'][$v['op_status']];
		}
		$this->assign('loglist', $loglist);
	}

	public function _doEditFilter($m) {
		if(!empty($_FILES['imgfile']['name'])){
			$this->savePathNew = C( "ADMIN_UPLOAD_DIR" ).'MemberData/'.$m->uid.'/' ;
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$info = $this->CUpload();
			$data['deal_image'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if($data['deal_image']) $m->deal_image = $data['deal_image'];
		$m->deal_user = $this->admin_id;
		$m->deal_time = time();
		return $m;
	}
	
	public function doEdit(){
        $model = D(ucfirst($this->getActionName()));
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        if (method_exists($this, '_doEditFilter')) {
            $model = $this->_doEditFilter($model);
        }
        $msg = '';
        //保存当前数据对象
        if ($result = $model->save()) { //保存成功
			$credits = intval($_POST['deal_credits']);
			$vd = M('member_data_info')->field("id,status,deal_image,deal_info,deal_credits,deal_user,data_name,uid")->find(intval($_POST['id']));
			//日志记录
			m('data_verify_log')->add(array(
				'type' 			=> 'memberdata',
				'did' 			=> $vd['id'],
				'op_status' 	=> $vd['status'],
				'op_image' 		=> $vd['deal_image'],
				'op_info' 		=> $vd['deal_info'],
				'op_credits' 	=> $vd['deal_credits'],
				'op_uid' 		=> $vd['deal_user'],
				'op_time' 		=> time()
			));
			//积分操作
			if($credits<>0) memberCreditsLog($vd['uid'],1,$credits,$vd['data_name']);
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $msg .= '修改成功';
            $this->success(L($msg));
        } else {
            //失败提示
            $msg .= '修改失败';
            $this->error(L($msg));
        }
		
	}
	
	public function _listFilter($list){
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$row=array();
		$this->assign("data_type",$Bconfig['DATA_TYPE']);
		$this->assign("data_status",$Bconfig['DATA_STATUS']);
		foreach($list as $key=>$v){
			$v['status_name'] = $Bconfig['DATA_STATUS'][$v['status']];
			$v['type'] = $Bconfig['DATA_TYPE'][$v['type']];
			$row[$key]=$v;
		}
		return $row;
	}
	
}
?>