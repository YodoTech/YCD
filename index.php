<?php
if(isset($_SERVER['HTTP_X_REWRITE_URL'])){
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
	$___s = explode(".",$_SERVER['REQUEST_URI']);
	$____s = explode("?",$_SERVER['REQUEST_URI']);
	$_SERVER['PATH_INFO'] = $____s[0];
	$GLOBALS['is_iis'] = true;
}
?>
<?php
    define('THINK_PATH',dirname(__FILE__).'./CORE/');
    define('APP_NAME',dirname(__FILE__).'App');
    define('APP_PATH',dirname(__FILE__).'./App/');
    define('APP_DEBUG',false);
    define('APP_PUBLIC_PATH',dirname(__FILE__).'./Public');

	define('BUILD_DIR_SECURE',true); 
	define('DIR_SECURE_FILENAME', 'default.html'); 
	define('DIR_SECURE_CONTENT', 'deney Access!'); 

    require(THINK_PATH.'/Core.php');
?>