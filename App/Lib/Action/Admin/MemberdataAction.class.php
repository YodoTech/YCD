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
	public function _editFilter(){
		setBackUrl();
	}
	
	public function doEdit(){
        $model = D(ucfirst($this->getActionName()));
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
        if ($result = $model->save()) { //保存成功
			$credits = intval($_POST['deal_credits']);
			$vd = M('member_data_info')->field("data_name,uid")->find(intval($_POST['id']));
			if($credits<>0) memberCreditsLog($vd['uid'],1,$credits,$vd['data_name']);
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('修改成功'));
        } else {
            //失败提示
            $this->error(L('修改失败'));
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