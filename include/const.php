<?
define('DOMAIN_WEB', 'kyoe.blogcocktail.com/facechart/web');
define('DOMAIN_MOBILE', 'kyoe.blogcocktail.com/facechart/mobile');

## DIR ##
define('DIR_ROOT', "{$ROOTPATH}"); // 루트
define('DIR_LIB', DIR_ROOT.'/lib'); // lib
define('DIR_WEB', DIR_ROOT.'/web'); // 웹
define('DIR_FILE', DIR_WEB.'/web/file'); // 파일

define('DIR_MOBILE', DIR_ROOT.'/mobile'); // 모바일
define('DIR_MOBILE_FILE', DIR_ROOT.'/mobile/file'); // 모바일 파일

## URL ##
define('URL_WEB', 'http://'.DOMAIN_WEB); // 웹
define('URL_FILE', 'http://'.DOMAIN_WEB.'/file'); // 파일

define('URL_MOBILE', 'http://'.DOMAIN_MOBILE); // 모바일
define('URL_MOBILE', 'http://'.DOMAIN_MOBILE.'/file'); // 모바일 파일

define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'kyoe');
define('MYSQL_PW', 'testtest');
define('MYSQL_DB', 'test');
define('MYSQL_PORT', 3306);
define('MYSQL_SOCKET', null);
?>
