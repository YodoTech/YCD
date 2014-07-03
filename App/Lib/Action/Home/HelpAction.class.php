<?php
// 本类由系统自动生成，仅供测试用途
class HelpAction extends HCommonAction {
    public function index(){
		$is_subsite=false;
		$typeinfo = get_type_infos();
		if(intval($typeinfo['typeid'])<1){
			$typeinfo = get_area_type_infos($this->siteInfo['id']);
			$is_subsite=true;
		}

		$typeid = $typeinfo['typeid'];
		$typeset = $typeinfo['typeset'];
		//left
		$listparm['type_id']=$typeid;
		$listparm['limit']=20;
		if($is_subsite===false) $leftlist = getTypeList($listparm);
		else{
			$listparm['area_id'] = $this->siteInfo['id'];
			$leftlist = getAreaTypeList($listparm);
		}
		$this->assign("leftlist",$leftlist);
		$this->assign("cid",$typeid);
		
		if($typeset==1){
			$parm['pagesize']=15;
			$parm['type_id']=$typeid;
			if($is_subsite===false){
				$list = getArticleList($parm);
				$vo = D('Acategory')->find($typeid);
				if($vo['parent_id']<>0) $this->assign('cname',D('Acategory')->getFieldById($vo['parent_id'],'type_name'));
				else $this->assign('cname',$vo['type_name']);
			}
			else{
				$vo = D('Aacategory')->find($typeid);
				if($vo['parent_id']<>0) $this->assign('cname',D('Aacategory')->getFieldById($vo['parent_id'],'type_name'));
				else $this->assign('cname',$vo['type_name']);
				$parm['area_id']= $this->siteInfo['id'];
				$list = getAreaArticleList($parm);
			}
			$this->assign("vo",$vo);
			$this->assign("list",$list['list']);
			$this->assign("pagebar",$list['page']);
		}else{
			if($is_subsite===false){
				$vo = D('Acategory')->find($typeid);
				if($vo['parent_id']<>0) $this->assign('cname',D('Acategory')->getFieldById($vo['parent_id'],'type_name'));
				else $this->assign('cname',$vo['type_name']);
			}else{
				$vo = D('Aacategory')->find($typeid);
				if($vo['parent_id']<>0) $this->assign('cname',D('Aacategory')->getFieldById($vo['parent_id'],'type_name'));
				else $this->assign('cname',$vo['type_name']);
			}
			$this->assign("vo",$vo);
		}
		
		$this->display($typeinfo['templet']);
    }
	
	public function view(){
		$id = intval($_GET['id']);
		if($_GET['type']=="subsite") $vo = M('article_area')->find($id);
		else $vo = M('article')->find($id);
		$this->assign("vo",$vo);

		//left
		$typeid = $vo['type_id'];
		$listparm['type_id']=$typeid;
		$listparm['limit']=20;
		if($_GET['type']=="subsite"){
			$listparm['area_id'] = $this->siteInfo['id'];
			$leftlist = getAreaTypeList($listparm);
		}else	$leftlist = getTypeList($listparm);
		
		$this->assign("leftlist",$leftlist);
		$this->assign("cid",$typeid);
		
		if($_GET['type']=="subsite"){
			$vop = D('Aacategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Aacategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}else{
			$vop = D('Acategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Acategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}

		$this->display();
	}
	
	public function kf(){
		$kflist = M("ausers")->where("is_kf=1")->select();
		$this->assign("kflist",$kflist);
		
		

		//left
		$listparm['type_id']=0;
		$listparm['limit']=20;
		if($_GET['type']=="subsite"){
			$listparm['area_id'] = $this->siteInfo['id'];
			$leftlist = getAreaTypeList($listparm);
		}else	$leftlist = getTypeList($listparm);
		
		$this->assign("leftlist",$leftlist);
		$this->assign("cid",$typeid);
		
		if($_GET['type']=="subsite"){
			$vop = D('Aacategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Aacategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}else{
			$vop = D('Acategory')->field('type_name,parent_id')->find($typeid);
			if($vop['parent_id']<>0) $this->assign('cname',D('Acategory')->getFieldById($vop['parent_id'],'type_name'));
			else $this->assign('cname',$vop['type_name']);
		}

		$this->display();
	}
	
	public function tuiguang(){
		$_P_fee=get_global_setting();
		$this->assign("reward",$_P_fee);	
		$field = " m.id,m.user_name,sum(ml.affect_money) jiangli ";
		$list = M("members m")->field($field)->join(" ".C('DB_PREFIX')."member_moneylog ml ON m.id = ml.target_uid ")->where("ml.type=13")->group("ml.uid")->order('jiangli desc')->limit(10)->select();
		$this->assign("list",$list);	
		
		$this->display();
	}
	
}