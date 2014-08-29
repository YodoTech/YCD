<?php
// 全局设置
class MapAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
		$field= 'id,title,map_writer,map_time';
		$this->_list(D('Map'),$field,'','id','DESC');
        $this->display();
    }
	
    public function _addFilter()
    {
		//...
    }

	public function _doAddFilter($m)
	{
		$m->map_time=time();
		$m->map_writer = session("admin_user_name");
		return $m;
	}

	public function _doEditFilter($m)
	{
		return $m;
	}

	public function _editFilter($id)
	{
		//...
	}
	
	public function _listFilter($list)
	{
		return $list;
	}
	
}
?>