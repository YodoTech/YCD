<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$
class TagLibHtmlA extends TagLib{
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1不闭合） alias 标签别名 level 嵌套层次
        'commonBtn'=>array('attr'=>'value,style,action,type','close'=>0),
        'input'=>array('attr'=>'id,style,value,tip,class','close'=>0),
        'timer'=>array('attr'=>'id,style,value,tip,arg','close'=>0),
        'radio'=>array('attr'=>'id,style,value,datakey,vt,tip,separator','close'=>0),
        'text'=>array('attr'=>'id,style,value,tip,class,addstr','close'=>0),
        'editor'=>array('attr'=>'id,style,w,h,type,value,type','close'=>0),
        'select'=>array('attr'=>'id,style,value,datakey,vt,tip,default,ishtml,NoDefalut,class','close'=>0),
        'checkbox'=>array('attr'=>'name,checkboxes,checked,separator','close'=>0),
        'user'=>array('attr'=>'id,uname','close'=>0),
		'tixianing'=>array('attr'=>'id,uname','close'=>0),
		'tixianwait'=>array('attr'=>'id,uname','close'=>0),
		'tixian'=>array('attr'=>'id,uname','close'=>0),//新增提现编辑信息扩展功能 添加人：fanyelei 添加时间：2012-12-02 09:10
        );
 
    public function _user($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'user');
        $uid      	= $tag['id'];                //文字
        $uname      = $tag['uname'];                //样式名
		$parseStr="";
		$parseStr = '<a onclick="loadUser({$'.$uid.'},\'{$'.$uname.'}\')" href="javascript:void(0);">{$'.$uname.'}</a>';
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * commonBtn标签解析
     * 格式： <html:commonBtn type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _commonBtn($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'commonBtn');
        $value      = $tag['value'];                //文字
        $style      = $tag['style'];                //样式名
        $action     = $tag['action'];                //点击
        $type       = $tag['type'];                //按钮类型
		
		$parseStr="";
		
        if($type=="jsfun") {
			$parseStr = '<a onclick="'.$action.'" class="btn_a" href="javascript:void(0);">';
			if(!empty($style)) $parseStr .= '<span class="'.$style.'">'.$value.'</span>';
			else  $parseStr .= '<span>'.$value.'</span>';
            $parseStr .= '</a>';
        }else {
			$parseStr = '<a class="btn_a" href="'.$action.'">';
			if(!empty($style)) $parseStr .= '<span class="'.$style.'">'.$value.'</span>';
			else  $parseStr .= '<span>'.$value.'</span>';
            $parseStr .= '</a>';
        }

        return $parseStr;
    }
    /**
     +----------------------------------------------------------
     * input标签解析
     * 格式： <html:input type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _input($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'input');
        $id      	= $tag['id'];                //name 和 id
        $value      = $tag['value']?$tag['value']:'';  //文本框值
        $addstr     = $tag['addstr']?$tag['addstr']:'';  //文本框值
        $tip     	= $tag['tip'];                //span tip提示内容
        $style      = $tag['style'];                //附加样式 style="widht:100"
        $className  = $tag['class']?" ".$tag['class']:'';                //附加样式 style="widht:100"
		
		$parseStr="";
		
        if($tip) {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<input name="'.$id.'" id="'.$id.'" '.$style.' class="input'.$className.'" type="text" value="'.$value.'" '.$addstr.'><span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        }else {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<input name="'.$id.'" id="'.$id.'" '.$style.' class="input'.$className.'" type="text" value="'.$value.'" '.$addstr.'>';
        }

        return $parseStr;
    }
    /**
     +----------------------------------------------------------
     * timer标签解析
     * 格式： <html:input type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _timer($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'input');
        $id      	= $tag['id'];                //name 和 id
        $value      = $tag['value']?$tag['value']:'';  //文本框值
        $class      = $tag['class']?" ".$tag['class']:'';  //文本框值
        $arg      	= $tag['arg']?$tag['arg']:'';  //文本框值
        $tip     	= $tag['tip'];                //span tip提示内容
        $style      = $tag['style'];                //附加样式 style="widht:100"
		
		$parseStr="";
		
        if($tip) {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<input onclick="WdatePicker('.$arg.');" name="'.$id.'" id="'.$id.'" '.$style.' class="input'.$class.'" type="text" value="'.$value.'"><span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        }else {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<input onclick="WdatePicker('.$arg.');" name="'.$id.'" id="'.$id.'" '.$style.' class="input'.$class.'" type="text" value="'.$value.'">';
        }

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * radio标签解析
     * 格式： <html:radio type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _radio($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'radio');
        $id      	= $tag['id'];                //name 和 id
        $style      = $tag['style']?$tag['style']:'';                //附加样式 style="widht:100"
        $tip      	= $tag['tip'];                //附加样式 style="widht:100"
        $value      = $tag['value']?$tag['value']:'';  //(key|value)|text,当前默认选中一维时key指键,value指值
        $default    = $tag['default']?$tag['default']:'';  //默认数据，不是动态获取的，以value|text,value1|text1的方式传入
        $datakey    = $tag['datakey'];                //要排列的内容以模板内赋值的名称传入,支持一维和二维
        $key     	= $tag['vt'];                //  valuekey|textkey,值键和文本健//二维数组时才需要
        $separator  = $tag['separator']?$tag['separator']:"&nbsp;&nbsp;&nbsp;&nbsp;";			//分隔符
        $addstr     = $tag['addstr']?$tag['addstr']:'';  //符加参数等
		$data = $this->tpl->get($datakey);//以名称获取模板变量

		$valueto = explode("|",$value);
 		if($valueto[0])	$valuekv = explode(".",$valueto[1]);
		$parseStr="";
		if($style) $style='style="'.$style.'"';
		$default_array=explode(",",$default);
		$default=array();
		foreach($default_array as $dkey=>$dv){
			$dxkv = explode("|",$dv);
			$default[$dxkv[0]] = $dxkv[1];
		}
		
        if($key) {
			if(empty($valuekv[0])) $valuekv[0]='_X';
			$keyto = explode("|",$key);
			$parseStr .='<php>$i=0;foreach($'.$datakey.' as $k=>$v){</php>';
			$parseStr .='<php> ';
			$parseStr .='if("!'.$valueto[0].'" && $i==0){';
			$parseStr .='</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$v["'.$keyto[0].'"]}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> ';
			$parseStr .='}elseif($'.$valuekv[0].'["'.$valuekv[1].'"]&&$v["'.$valueto[0].'"]==$'.$valuekv[0].'["'.$valuekv[1].'"]){';
			$parseStr .='</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$v["'.$keyto[0].'"]}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> ';
			$parseStr .='}else{';
			$parseStr .='</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$v["'.$keyto[0].'"]}" id="'.$id.'_{$i}" '.$addstr.'/>';	
			$parseStr .='<php>}</php>';
			$parseStr.='<label for="'.$id.'_{$i}">{$v["'.$keyto[1].'"]}</label>'.$separator;
			$parseStr .='<php>$i++;}</php>';
        }elseif($datakey && !empty($value)){
			$parseStr .='<php>$i=0;foreach($'.$datakey.' as $k=>$v){</php>';
			$parseStr .='<php> if(strlen("'.$valueto[0].'1")==1&&$i==0){</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> }elseif("'.$valueto[0].'1"=="key1"&&$k==$'.$valuekv[0].'["'.$valuekv[1].'"]){</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> }elseif("'.$valueto[0].'1"=="value1"&&$v==$'.$valuekv[0].'["'.$valuekv[1].'"]){</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> }else{ </php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" '.$addstr.'/>';	
			$parseStr .='<php> } </php>';
			$parseStr.='<label for="'.$id.'_{$i}">{$v}</label>'.$separator;
			$parseStr .='<php>$i++;}</php>';
		}elseif($datakey && empty($value)){
			$parseStr .='<php>$i=0;foreach($'.$datakey.' as $k=>$v){</php>';
			$parseStr .='<php> if($i==0){</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> }else{ </php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" '.$addstr.'/>';	
			$parseStr .='<php> } </php>';
			$parseStr.='<label for="'.$id.'_{$i}">{$v}</label>'.$separator;
			$parseStr .='<php>$i++;}</php>';
		}else{
			if(empty($valuekv[0])){
				$valuekv[0]='_X';
				$valuekv[1]='_Y';
			}
			$parseStr .='<php>$i=0;$___KEY='.var_export($default,true).';</php>';
			$parseStr .='<php>foreach($___KEY as $k=>$v){</php>';
			
			$parseStr .='<php>if(strlen("1'.$valueto[0].'")==1 && $i==0){</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php>}elseif(("'.$valueto[0].'1"=="key1"&&$'.$valuekv[0].'["'.$valuekv[1].'"]==$k)||("'.$valueto[0].'"=="value"&&$'.$valuekv[0].'["'.$valuekv[1].'"]==$v)){';
			$parseStr .='</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" checked="checked" '.$addstr.'/>';
			$parseStr .='<php> }else{</php>';
			$parseStr.='<input type="radio" name="'.$id.'" value="{$k}" id="'.$id.'_{$i}" '.$addstr.'/>';
			$parseStr .='<php>}</php>';
			$parseStr.='<label for="'.$id.'_{$i}">{$v}</label>'.$separator;
			$parseStr .='<php>$i++;</php>';
			$parseStr .='<php>}</php>';
        }
		if($tip) $parseStr.='<span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        return $parseStr;
    }
     /**
     +----------------------------------------------------------
     * text标签解析
     * 格式： <html:text type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _text($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'input');
        $id      	= $tag['id'];                //name 和 id
        $value      = $tag['value']?$tag['value']:'';  //文本框值
        $tip     	= $tag['tip'];                //span tip提示内容
        $style      = $tag['style']?$tag['style']:'';                //附加样式 style="widht:100"
	    $className  = $tag['class']?" ".$tag['class']:'';                //附加样式 style="widht:100"
	    $addstr  	= $tag['addstr']?$tag['addstr']:'';                //附加样式 style="widht:100"
	
		$parseStr="";
		
        if($tip) {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<textarea name="'.$id.'" id="'.$id.'" '.$style.' class="areabox'.$className.'" '.$addstr.'>'.$value.'</textarea><span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        }else {
			if($style) $style='style="'.$style.'"';
			$parseStr = '<textarea name="'.$id.'" id="'.$id.'" '.$style.' class="areabox'.$className.'" '.$addstr.'>'.$value.'</textarea>';
        }

        return $parseStr;
    }

   /**
     +----------------------------------------------------------
     * editor标签解析 插入可视化编辑器
     * 格式： <html:editor id="editor" name="remark" type="FCKeditor" style="" >{$vo.remark}</html:editor>
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _editor($attr,$content)
    {
        $tag        =	$this->parseXmlAttr($attr,'editor');
        $id			=	$tag['id'];
        $style   	=	$tag['style']?$tag['style']:'';
        $value   	=	$tag['value']?$tag['value']:'';
        $type   	=	$tag['type'];
        $width		=	!empty($tag['w'])?$tag['w']: '100%';
        $height     =	!empty($tag['h'])?$tag['h'] :'320px';
        $type       =   $tag['type'] ;
        switch(strtoupper($type)) {
            case 'KISSY':
                $parseStr   =	'<!-- 编辑器调用开始 -->
				<textarea name="'.$id.'" id="'.$id.'" style="width:'.$width.';height:'.$height.';'.$style.'">'.$value.'</textarea>
				<script>
				
					loadEditor("'.$id.'");
				
				</script>
				<!-- 编辑器调用结束 -->';
                break;
            default :
                $parseStr  =  '<textarea  name="'.$id.'" id="'.$id.'" style="width:'.$width.';height:'.$height.';'.$style.'" >'.$value.'</textarea>';
        }

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * select标签解析
     * 格式： <html:select options="name" selected="value" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _select($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'select');
        $id      	= $tag['id'];                //name 和 id
        $style      = $tag['style']?$tag['style']:'';                //附加样式 style="widht:100"
        $ishtml     = $tag['ishtml'];                //是用静态还是动态
        $tip      	= $tag['tip'];                //附加样式 style="widht:100"
        $value      = $tag['value']?$tag['value']:'';  //(key|value)|text,当前默认选中一维时'key'指键,'value'指值
        $NoDefalut  = $tag['nodefalut']?$tag['nodefalut']:false;  //(key|value)|text,当前默认选中一维时'key'指键,'value'指值
        $class      = $tag['class']?" ".$tag['class']:'';  //(key|value)|text,当前默认选中一维时'key'指键,'value'指值
        $default    = $tag['default']?$tag['default']:'--请选择--';  //(key|value)|text,当前默认选中一维时'key'指键,'value'指值
        $datakey    = $tag['datakey'];                //要排列的内容以模板内赋值的名称传入,支持一维和二维
        $vt     	= $tag['vt'];                //  valuekey|textkey,值键和文本健//二维数组时才需要
        $addstr     = $tag['addstr']?$tag['addstr']:'';  //符加参数等
		$data = $this->tpl->get($datakey);//以名称获取模板变量
		
		$parseStr="";
 		$valueto = explode("|",$value);
		if($style) $style='style="'.$style.'"';
 		if($valueto[0])	$valuekv = explode(".",$valueto[1]);
		
		if($ishtml){//静态
 		$datahtml = $this->tpl->get($valuekv[0]);//以名称获取模板变量
		$cvalue = $datahtml[$valuekv[1]];
       	if($vt) {
			$keyto = explode("|",$vt);
			$parseStr .='<select name="'.$id.'" id="'.$id.'" '.$style.' '.$addstr.' class="c_select'.$class.'">';
			if(!$NoDefalut) $parseStr .='<option value="">'.$default.'</option>';
			foreach($data as $k => $v){
				if($valueto[0] && $v[$valueto[0]]==$cvalue) $parseStr .='<option value="'.$v[$keyto[0]].'" selected="selected">'.$v[$keyto[1]].'</option>';
				else $parseStr .='<option value="'.$v[$keyto[0]].'">'.$v[$keyto[1]].'</option>';
			}
        }else{
			$parseStr .='<select name="'.$id.'" id="'.$id.'" '.$style.' '.$addstr.' class="c_select'.$class.'">';
			if(!$NoDefalut) $parseStr .='<option value="">'.$default.'</option>';
			foreach($data as $k => $v){
				if(($valueto[0]=='key'&&$cvalue==$k)||($valueto[0]=='value'&&$cvalue==$v)) $parseStr .='<option value="'.$k.'" selected="selected">'.$v.'</option>';
				else $parseStr .='<option value="'.$k.'">'.$v.'</option>';
			}
		}
        $parseStr   .= '</select>';
		}else{//静态END 动态START
        if($vt) {
			if(empty($valuekv[0])) $valuekv[0]='_X';
			$keyto = explode("|",$vt);
			$parseStr .='<select name="'.$id.'" id="'.$id.'" '.$style.' '.$addstr.' class="c_select'.$class.'">';
			if(!$NoDefalut) $parseStr .='<option value="">'.$default.'</option>';
			$parseStr .='<php>foreach($'.$datakey.' as $key=>$v){</php>';
			
			$parseStr .='<php> ';
			$parseStr .='if("'.$valueto[0].'" && $v["'.$valueto[0].'"]==$'.$valuekv[0].'["'.$valuekv[1].'"]){';
			$parseStr .='</php>';
			$parseStr .='<option value="{$v.'.$keyto[0].'}" selected="selected">{$v.'.$keyto[1].'}</option>';
			$parseStr .='<php> ';
			$parseStr .='}else{';
			$parseStr .='</php>';
			$parseStr .='<option value="{$v.'.$keyto[0].'}">{$v.'.$keyto[1].'}</option>';
			$parseStr .='<php> ';
			$parseStr .='}}';
			$parseStr .='</php>';
        }else{
			if(empty($valuekv[0])) $valuekv[0]='_X';
			$parseStr .='<select name="'.$id.'" id="'.$id.'" '.$style.' '.$addstr.' class="c_select'.$class.'">';
			if(!$NoDefalut) $parseStr .='<option value="">'.$default.'</option>';
			$parseStr .='<php>foreach($'.$datakey.' as $key=>$v){</php>';
			$parseStr .='<php> ';
			$parseStr .='if($'.$valuekv[0].'["'.$valuekv[1].'"]==$key && $'.$valuekv[0].'["'.$valuekv[1].'"]!=""){';
			$parseStr .='</php>';
			$parseStr .='<option value="{$key}" selected="selected">{$v}</option>';
			$parseStr .='<php> ';
			$parseStr .='}else{';
			$parseStr .='</php>';
			$parseStr .='<option value="{$key}">{$v}</option>';
			$parseStr .='<php> ';
			$parseStr .='}}';
			$parseStr .='</php>';
		}
        $parseStr   .= '</select>';
		}//动态END
		if($tip) $parseStr.='<span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * checkbox标签解析
     * 格式： <htmlA:checkbox type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _checkbox($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'checkbox');
        $id      	= $tag['id'];                //name 和 id
        $style      = $tag['style'];                //附加样式 style="widht:100"
        $tip      	= $tag['tip'];                //附加样式 style="widht:100"
        $value      = $tag['value']?$tag['value']:'';  //(key|value)|text,当前默认选中一维时key指键,value指值
        $datakey    = $tag['datakey'];                //要排列的内容以模板内赋值的名称传入,支持一维和二维
        $key     	= $tag['vt'];                //  valuekey|textkey,值键和文本健//二维数组时才需要
        $separator  = $tag['separator']?$tag['separator']:"&nbsp;&nbsp;&nbsp;&nbsp;";			//分隔符

		$valueto = explode("|",$value);
		$parseStr="";
		if($style) $style='style="'.$style.'"';
        if($key) {
			$keyto = explode("|",$key);
			$parseStr .='<php>$i=0;foreach($'.$datakey.' as $key=>$v){';
			$parseStr .='if("'.$valueto[0].'" && in_array($v["'.$keyto[0].'"],$'.$valueto[1].'){';
			$parseStr .='</php>';
			$parseStr .='<input type="checkbox" name="'.$id.'[]" id="'.$id.'_{$i}" value="{$v[\''.$keyto[0].'\']}" checked="checked">';
			$parseStr .='<php>}else{</php>';
			$parseStr .='<input type="checkbox" name="'.$id.'[]" id="'.$id.'_{$i}" value="{$v[\''.$keyto[0].'\']}">';
			$parseStr .='<php>}</php>';
			$parseStr .='<label for="'.$id.'_{$i}">{$v[\''.$keyto[1].'\']}</label>'.$separator;
			$parseStr .='<php>$i++;}</php>';
        }else {
			$i=0;
			$parseStr .='<php>$i=0;foreach($'.$datakey.' as $key=>$v){';
			$parseStr .='if(is_array($'.$valueto[1].') && in_array($key,$'.$valueto[1].')){';
			$parseStr .='</php>';
			$parseStr .='<input type="checkbox" name="'.$id.'[]" id="'.$id.'_{$i}" value="{$key}" checked="checked">';
			$parseStr .='<php>}else{</php>';
			$parseStr .='<input type="checkbox" name="'.$id.'[]" id="'.$id.'_{$i}" value="{$key}">';
			$parseStr .='<php>}</php>';
			$parseStr .='<label for="'.$id.'_{$i}">{$v}</label>'.$separator;
			$parseStr .='<php>$i++;}</php>';
        }
		if($tip) $parseStr.='<span id="tip_'.$id.'" class="tip">'.$tip.'</span>';
        return $parseStr;
    }
/*****************************新增提现编辑信息扩展功能 添加人：fanyelei 添加时间：2012-12-02 09:10**********************/
 	public function _tixian($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'user');
        $uid      	= $tag['id'];                //文字
        $uname      = $tag['uname'];                //样式名
		$parseStr="";
		$parseStr = '<a onclick="loadTixian({$'.$uid.'},\'{$'.$uname.'}\')" href="javascript:void(0);">编辑</a>';
        return $parseStr;
    }
	public function _tixianing($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'user');
        $uid      	= $tag['id'];                //文字
        $uname      = $tag['uname'];                //样式名
		$parseStr="";
		$parseStr = '<a onclick="loadTixianing({$'.$uid.'},\'{$'.$uname.'}\')" href="javascript:void(0);">编辑</a>';
        return $parseStr;
    }

public function _tixianwait($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'user');
        $uid      	= $tag['id'];                //文字
        $uname      = $tag['uname'];                //样式名
		$parseStr="";
		$parseStr = '<a onclick="loadTixianwait({$'.$uid.'},\'{$'.$uname.'}\')" href="javascript:void(0);">编辑</a>';
        return $parseStr;
    }
/*****************************新增提现编辑信息扩展功能 添加人：fanyelei 添加时间：2012-12-02 09:10**********************/
}
?>