<?php

//获取特定栏目下文章列表
function getAreaArticleList($parm){
	if(empty($parm['type_id'])) return;
	$map['type_id'] = $parm['type_id'];
	$Osql="id DESC";
	$field="id,title,art_set,art_time,art_url,area_id";
	//查询条件 
	if($parm['pagesize']){
		//分页处理
		import("ORG.Util.Page");
		$count = M('article_area')->where($map)->count('id');
		$p = new Page($count, $parm['pagesize']);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
	}else{
		$page="";
		$Lsql="LIMIT {$parm['limit']}";
	}

	$data = M('article_area')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();

	$suffix=C("URL_HTML_SUFFIX");
	$typefix = get_type_leve_area_nid($map['type_id'],$parm['area_id']);

	$typeu = implode("/",$typefix);
	foreach($data as $key=>$v){
		if($v['art_set']==1) $data[$key]['arturl'] = (stripos($v['art_url'],"http://")===false)?"http://".$v['art_url']:$v['art_url'];
		//elseif(count($typefix)==1) $data[$key]['arturl'] = 
		else $data[$key]['arturl'] = MU("Home/{$typeu}","article",array("id"=>"id-".$v['id'],"suffix"=>$suffix));
	}
	$row=array();
	$row['list'] = $data;
	$row['page'] = $page;
	
	return $row;
}

//获取下级或者同级栏目列表
function getAreaTypeList($parm){
	//if(empty($parm['type_id'])) return;
	$Osql="sort_order DESC";
	$field="id,type_name,type_set,add_time,type_url,type_nid,parent_id,area_id";
	//查询条件 
	$Lsql="{$parm['limit']}";
	$pc = D('Aacategory')->where("parent_id={$parm['type_id']} AND area_id={$parm['area_id']}")->count('id');
	if($pc>0){
		$map['is_hiden'] = 0;
		$map['parent_id'] = $parm['type_id'];
		$map['area_id'] = $parm['area_id'];
		$data = D('Aacategory')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	}elseif(!isset($parm['notself'])){
		$map['is_hiden'] = 0;
		$map['parent_id'] = D('Aacategory')->getFieldById($parm['type_id'],'parent_id');
		$map['area_id'] = $parm['area_id'];
		$data = D('Aacategory')->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
	}

	//链接处理
	$typefix = get_type_leve_area_nid($parm['type_id'],$parm['area_id']);
	$typeu = $typefix[0];
	$suffix=C("URL_HTML_SUFFIX");
	foreach($data as $key=>$v){
		if($v['type_set']==2){
			if(empty($v['type_url'])) $data[$key]['turl']="javascript:alert('请在后台添加此栏目链接');";
			else $data[$key]['turl'] = $v['type_url'];
		}
		elseif($v['parent_id']==0&&count($typefix)==1) $data[$key]['turl'] = MU("Home/{$v['type_nid']}/index","typelist",array("id"=>$v['id'],"suffix"=>$suffix));
		else $data[$key]['turl'] = MU("Home/{$typeu}/{$v['type_nid']}","typelist",array("id"=>$v['id'],"suffix"=>$suffix));
	}
	$row=array();
	$row = $data;
	
	return $row;
}