<?php
/**
 * 分页类
*/

class Page
{
    public     $firstRow;        //起始行数
 
    public     $listRows;        //列表每页显示行数
     
    protected  $total_pages;      //总页数
 
    protected  $total_rows;       //总行数
     
    protected  $now_page;         //当前页数
     
    protected  $method  = 'defalut'; //处理情况 Ajax分页 Html分页(静态化时) 普通get方式 
     
    protected  $parameter = '';
     
    protected  $page_name;        //分页参数的名称
     
    protected  $ajax_func_name;
     
    protected  $plus = 4;         //分页偏移量
     
    protected  $url;
     
     
    /**
     * 构造函数
     * @param unknown_type $data
     */
    public function __construct($totalRows, $listRows = '', $data = array())
    {
        $this->total_rows = $totalRows;
        if(!empty($listRows)) {
            $this->listRows = intval($listRows);
        }
 
        $this->parameter         = !empty($data['parameter']) ? $data['parameter'] : '';
        $this->listRows         = !empty($this->listRows) && $this->listRows <= 100 ? $this->listRows : 20;
        $this->total_pages       = ceil($this->total_rows / $this->listRows);
        $this->page_name         = !empty($data['page_name']) ? $data['page_name'] : 'p';
        $this->ajax_func_name    = !empty($data['ajax_func_name']) ? $data['ajax_func_name'] : '';
         
        $this->method           = !empty($data['method']) ? $data['method'] : '';
         
         
        /* 当前页面 */
        if(!empty($data['now_page']))
        {
            $this->now_page = intval($data['now_page']);
        }else{
            $this->now_page   = !empty($_GET[$this->page_name]) ? intval($_GET[$this->page_name]):1;
        }
        $this->now_page   = $this->now_page <= 0 ? 1 : $this->now_page;
     
         
        if(!empty($this->total_pages) && $this->now_page > $this->total_pages)
        {
            $this->now_page = $this->total_pages;
        }
        $this->firstRow = $this->listRows * ($this->now_page - 1);
    }   
     
    /**
     * 得到当前连接
     * @param $page
     * @param $text
     * @return string
     */
    protected function _get_link($page, $text, $extr='')
    {
        switch ($this->method) {
            case 'ajax':
                $parameter = '';
                if($this->parameter)
                {
                    $parameter = ','.$this->parameter;
                }
                return '<a onclick="' . $this->ajax_func_name . '(\'' . $page . '\''.$parameter.')" href="javascript:void(0)"'.$extr.'>' . $text . '</a>' . "\n";
            break;

            case 'html':
                $url = str_replace('?', $page,$this->parameter);
                return '<a href="' .$url . '"'.$extr.'>' . $text . '</a>' . "\n";
            break;

            default:
                return '<a href="' . $this->_get_url($page) . '"'.$extr.'>' . $text . '</a>' . "\n";
            break;
        }
    }
     
     
    /**
     * 设置当前页面链接
     */
    protected function _set_url()
    {
        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$this->page_name]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        if(!empty($params))
        {
            $url .= '&';
        }
        $this->url = $url;
    }
     
    /**
     * 得到$page的url
     * @param $page 页面
     * @return string
     */
    protected function _get_url($page)
    {
        if($this->url === NULL)
        {
            $this->_set_url();   
        }
    //  $lable = strpos('&', $this->url) === FALSE ? '' : '&';
        return $this->url . $this->page_name . '=' . $page;
    }
     
     
    /**
     * 得到第一页
     * @return string
     */
    protected function first_page($name = '第一页')
    {
        if($this->now_page > 5)
        {
            return $this->_get_link('1', $name);
        }   
        return '';
    }
     
    /**
     * 最后一页
     * @param $name
     * @return string
     */
    protected function last_page($name = '最后一页')
    {
        if($this->now_page < $this->total_pages - 5)
        {
            return $this->_get_link($this->total_pages, $name);
        }   
        return '';
    }  
     
    /**
     * 上一页
     * @return string
     */
    protected function up_page($name = '上一页')
    {
        if($this->now_page != 1)
        {
            return $this->_get_link($this->now_page - 1, $name);
        }
        return '';
    }
     
    /**
     * 下一页
     * @return string
     */
    protected function down_page($name = '下一页')
    {
        if($this->now_page < $this->total_pages)
        {
            return $this->_get_link($this->now_page + 1, $name);
        }
        return '';
    }
 
    /**
     * 分页样式输出
     * @return string
     */
    public function show()
    {
        if($this->total_rows < 1)
        {
            return '';
        }
        
        $className = 'show_' . c("DEFAULT_THEME");
         
        $classNames = get_class_methods($this);
 
        if(in_array($className, $classNames))
        {
            return $this->$className();
        }
        return '';
    }
    
    /*
    <li>&lt;</li>
    <li><a href="#">1</a></li>
    <li><a href="#">2</a></li>
    <li><a href="#">3</a></li>
    <li><a href="#">4</a></li>
    <li><a href="#">5</a></li>
    <li><a href="#">6</a></li>
    <li class="li1">…</li>
    <li><a href="#">7</a></li>
    <li><a href="#">8</a></li>
    <li class="li2"><a href="#">&gt;</a></li>
    */
    protected function show_orange()
    {
        if($this->total_pages != 1)
        {
            $return = '';
            //上一页
            if($this->now_page != 1) {
                $return .= '<li class="li2">'.$this->_get_link($this->now_page - 1, '&lt;').'</li>';
            } else {
                $return .= '<li>&lt;</li>';
            }
            for($i = 1;$i<=$this->total_pages;$i++)
            {
                if($i == $this->now_page)
                {
                    $return .= "<li class='current'><span>$i</span></li>\n";
                }
                else
                {
                    if($this->now_page-$i>=3 && $i != 1)
                    {
                        $return .="<li class='li1'>...</li>\n";
                        $i = $this->now_page-2;
                    }
                    else
                    {
                        if($i >= $this->now_page+4 && $i != $this->total_pages)
                        {
                            $return .= "<li class='li1'>...</li>\n";
                            $i = $this->total_pages;
                        }
                        $return .= '<li>'.$this->_get_link($i, $i).'</li>'."\n";
                    }
                }
            }
            //下一页
            if($this->now_page < $this->total_pages) {
                $return .= '<li class="li2">'.$this->_get_link($this->now_page + 1, '&gt;').'</li>';
            } else {
                $return .= '<li>&gt;</li>';
            }
            return $return;
        }
    }
     
    protected function show_()
    {
        $plus = $this->plus;
        if( $plus + $this->now_page > $this->total_pages)
        {
            $begin = $this->total_pages - $plus * 2;
        }else{
            $begin = $this->now_page - $plus;
        }
         
        $begin = ($begin >= 1) ? $begin : 1;
        $return = '';
        $return .= $this->first_page();
        $return .= $this->up_page();
        for ($i = $begin; $i <= $begin + $plus * 2;$i++)
        {
            if($i>$this->total_pages)
            {
                break;
            }
            if($i == $this->now_page)
            {
                $return .= "<a class='now_page'>$i</a>\n";
            }
            else
            {
                $return .= $this->_get_link($i, $i) . "\n";
            }
        }
        $return .= $this->down_page();
        $return .= $this->last_page();
        return $return;
    }
    
    /*
    <div class="ui-block-a"><a href="#" data-role="button" class="a1">第一页</a></div>
    <div class="ui-block-b"><a href="#" data-role="button" class="a1">上一页</a></div>
    <div class="ui-block-c">1/27</div>
    <div class="ui-block-d"><a href="#" data-role="button">下一页</a></div>  
    <div class="ui-block-e"><a href="#" data-role="button">末 页</a></div>  
    */
    protected function show_mobile()
    {
        $return = '';
        if ($this->total_pages != 1) {
            $extr1 = ' data-role="button" class="ui-link ui-btn ui-shadow ui-corner-all" role="button"';
            $extr2 = ' data-role="button" class="a1 ui-link ui-btn ui-shadow ui-corner-all" role="button"';
            //第一页
            $return .= '<div class="ui-block-a">';
            if ($this->now_page != 1) {
                $return .= $this->_get_link('1', '第一页', $extr1);
            } else {
                $return .= '<a href="javascript:;"'.$extr2.'>第一页</a>';
            }
            $return .= '</div>';
            //上一页
            $return .= '<div class="ui-block-b">';
            if ($this->now_page != 1) {
                $return .= $this->_get_link($this->now_page - 1, '上一页', $extr1);
            } else {
                $return .= '<a href="javascript:;"'.$extr2.'>上一页</a>';
            }
            $return .= '</div>';
            //...
            $return .= '<div class="ui-block-c">'.$this->now_page.'/'.$this->total_pages.'</div>';
            //下一页
            $return .= '<div class="ui-block-d">';
            if ($this->now_page < $this->total_pages) {
                $return .= $this->_get_link($this->now_page + 1, '下一页', $extr1);
            } else {
                $return .= '<a href="javascript:;"'.$extr2.'>下一页</a>';
            }
            $return .= '</div>';
            //最后一页
            $return .= '<div class="ui-block-e">';
            if($this->now_page < $this->total_pages) {
                $return .= $this->_get_link($this->total_pages, '末&nbsp;页', $extr1);
            } else {
                $return .= '<a href="javascript:;"'.$extr2.'>末&nbsp;页</a>';
            }
            $return .= '</div>';
        }
        return $return;
    }
}