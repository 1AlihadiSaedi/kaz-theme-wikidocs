<?php
error_reporting(E_ALL);
ini_set('display_errors',(isset($_GET['debug']) && $_GET['debug']==1));
define('BASE',str_replace(['/','\\'],DIRECTORY_SEPARATOR,__DIR__.'/'));
if(version_compare(PHP_VERSION,'7.4.0')<0){die('Required at least PHP version 7.4.0, current version: '.PHP_VERSION);}
require_once(BASE.'functions.inc.php');
require_once(BASE.'classes/WikiDocs.class.php');
require_once(BASE.'classes/Localization.class.php');
require_once(BASE.'classes/Document.class.php');
require_once(BASE.'classes/Session.class.php');
require_once(BASE.'classes/SecurityFilters.class.php');
require_once(BASE."libraries/parsedown-1.8.0-beta-6/Parsedown.php");
require_once(BASE."libraries/parsedown-extra-0.8.1/ParsedownExtra.php");
require_once(BASE."libraries/parsedown-extended-1.1.2-modified/ParsedownExtended.php");
require_once(BASE."libraries/parsedown-filter-0.0.1/ParsedownFilter.php");
require_once(BASE."libraries/parsedown-plus-0.0.8/ParsedownPlus.php");
if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']=='https'){$_SERVER['HTTPS']='on';}
if(!file_exists(realpath(dirname(__FILE__))."/datasets/config.inc.php")){header("location:setup.php");}
if (isset($_COOKIE['wikidocs_lang'])) {$c = preg_replace('/[^a-z-]/', '', $_COOKIE['wikidocs_lang']); if (file_exists(BASE . 'localizations/' . $c . '.json')) { define('LANG', $c); }}
require_once("datasets/config.inc.php");
if(Session::getInstance()->isDebug()){$debug=true;}else{$debug=false;}
if(isset($_GET['debug'])){if(DEBUGGABLE && $_GET['debug']==1){$debug=true;Session::getInstance()->setDebug(true);}else{$debug=false;Session::getInstance()->setDebug(false);}}
ini_set("display_errors",$debug);
if(!defined("TIMEZONE")){define("TIMEZONE","default");}elseif(TIMEZONE!="default"){date_default_timezone_set(TIMEZONE);}
if(!defined("ALLOWED_IFRAME_HOSTS")){define("ALLOWED_IFRAME_HOSTS","www.youtube.com,www.youtube-nocookie.com,player.vimeo.com");}
$g_doc=strtolower(str_replace(array(" "),"-",($_GET['doc'] ?? '')));
if(substr($g_doc,-1)=="/"){$g_doc=substr($g_doc,0,-1);}
$g_doc=htmlspecialchars($g_doc,ENT_QUOTES,'UTF-8');
if(!strlen($g_doc)){$g_doc="homepage";}
$original_dir=str_replace("\\","/",realpath(dirname(__FILE__))."/");
$root_dir=substr($original_dir,0,strrpos($original_dir,(string)PATH));
define("DEBUG",$debug);
define("VERSION",file_get_contents(BASE."VERSION"));
define("HOST",(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER['HTTP_HOST']);
define("ROOT",$root_dir);
define("URL",HOST.PATH);
define("DIR",ROOT.PATH);
define("DOC",$g_doc);
if(isset($_GET['privacy'])){Session::getInstance()->privacyAgreement($_GET['privacy']);}
if(!file_exists(DIR.'sitemap.xml')){wdf_regenerate_sitemap();}
