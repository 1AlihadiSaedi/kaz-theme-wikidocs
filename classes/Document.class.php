<?php
/**
 * Document class — Multi-language support
 *
 * @package WikiDocs
 * @repository https://github.com/Zavy86/wikidocs
 */

const HTML_EVENTS = [
    "onerror", "onload", "onunload", "onbeforeunload",
    "onclick", "onauxclick", "ondblclick", "oncontextmenu",
    "onsubmit", "onreset", "onchange", "oninput", "oninvalid", "onselect", "onsearch",
    "onfocus", "onblur", "onfocusin", "onfocusout",
    "onkeydown", "onkeypress", "onkeyup",
    "onmousedown", "onmouseenter", "onmouseleave", "onmousemove", "onmouseout", "onmouseover", "onmouseup", "onmousewheel", "onwheel",
    "onpointerdown", "onpointerup", "onpointermove", "onpointerover", "onpointerout", "onpointerenter", "onpointerleave", "onpointercancel", "ongotpointercapture", "onlostpointercapture",
    "ontouchstart", "ontouchend", "ontouchmove", "ontouchcancel",
    "ondrag", "ondragend", "ondragenter", "ondragleave", "ondragover", "ondragstart", "ondrop", "ondragexit",
    "oncopy", "oncut", "onpaste",
    "ontoggle", "onbeforetoggle", "onformdata", "onslotchange",
    "onanimationstart", "onanimationend", "onanimationiteration", "ontransitionend", "ontransitionstart", "ontransitionrun", "ontransitioncancel",
    "onplay", "onplaying", "onpause", "onended", "onvolumechange", "ontimeupdate", "ondurationchange", "oncanplay", "oncanplaythrough", "onratechange", "onloadstart", "onloadeddata", "onloadedmetadata", "onprogress", "onwaiting", "onabort", "onemptied", "onseeked", "onseeking", "onstalled", "onsuspend",
    "onhashchange", "onpopstate", "onstorage", "onmessage", "ononline", "onoffline", "onpageshow", "onpagehide", "onafterprint", "onbeforeprint", "onresize", "onscroll", "onscrollend",
    "onopen", "onclose", "oncuechange", "onsecuritypolicyviolation",
    "onactivate", "onbeforeactivate", "onbeforecut", "onbeforeeditfocus", "onbeforeupdate",
    "oncontrolselect", "ondeactivate", "onerrorupdate", "onmove", "onmovestart", "onmoveend",
    "onpropertychange", "onresizestart", "onresizeend", "ontimeerror", "onafterupdate",
    "onbeforecopy", "onbeforedeactivate", "onbeforepaste", "onfilterchange", "onhelp",
    "onlosecapture", "onreadystatechange", "onselectstart",
];

final class Document{

    protected string $ID;
    protected string $PATH;
    protected string $URL;
    protected string $DIR;
    protected string $TITLE;
    protected string $VERSION;
    protected ?string $FILE;
    protected ?int $TIMESTAMP;

    /**
     * Resolve content file — tries content.{LANG}.md first, falls back to content.md.
     */
    private static function resolveContentFile(string $dir): ?string {
        $lang = defined('LANG') ? LANG : 'en';
        $langFile = $dir . 'content.' . $lang . '.md';
        if (file_exists($langFile)) return $langFile;
        $defaultFile = $dir . 'content.md';
        if (file_exists($defaultFile)) return $defaultFile;
        $glob = glob($dir . 'content.*.md');
        if (!empty($glob)) return $glob[0];
        return null;
    }

    public function __construct(string $id){
        $this->ID=$id;
        $this->PATH=PATH."datasets/documents/".$this->ID;
        $this->URL=URL.$this->ID;
        $this->DIR=ROOT.$this->PATH."/";
        $this->TITLE=self::getTitle($this->ID);
        $this->VERSION=(strlen($_GET['version']??'')?$_GET['version']:"latest");
        $this->FILE=self::resolveContentFile($this->DIR);
        $this->TIMESTAMP=null;
        if(file_exists($this->FILE ?? '')){$this->TIMESTAMP=filemtime($this->FILE);}
    }

    public function __get(string $property){return $this->{$property};}

    public function export():array{
        $properties_array=array();
        foreach($this as $key => $value){ $properties_array[$key]=$value; }
        return $properties_array;
    }

    public function loadContent($paths="WEB"):string{
        if($this->VERSION!=="latest"){
            $file_path=$this->DIR."versions/".$this->VERSION.".md";
            if(!file_exists($file_path)){$file_path=$this->FILE ?? '';}
        }else{
            $file_path = self::resolveContentFile($this->DIR) ?? '';
        }
        if(!strlen($file_path) || !file_exists($file_path)){return false;}
        $content=file_get_contents($file_path);
        switch(strtoupper(trim($paths))){
            case "WEB":
                $source=str_replace(array("{{APP_PATH}}","{{DOC_PATH}}"),array(PATH,$this->PATH."/"),$content);
                $source = $this->sanitizeHtml($source);
                break;
            case "FS":$source=str_replace(array("{{APP_PATH}}","{{DOC_PATH}}"),array(URL,$this->DIR."/"),$content);break;
            default:$source=str_replace(array("{{APP_PATH}}","{{DOC_PATH}}"),"",$content);
        }
        return $source;
    }

    public function sanitizeHtml($string) {
        static $dangerousTags = ['script','object','embed','form','meta','link','base','frame','frameset','applet','svg','math','style','template','textarea','noscript'];
        $parts = preg_split('/(```.*?```|`.*?`)/is', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($parts as &$part) {
            if (!preg_match('/^```.*```$|^`.*`$/is', $part)) {
                $part = self::sanitizeIframes($part);
                foreach (HTML_EVENTS as $event) { $part = str_ireplace($event, '&#111;' . substr($event, 1), $part); }
                foreach ($dangerousTags as $tag) { $part = str_ireplace(array("<".$tag, "</".$tag), array("&lt;".$tag, "&lt;/".$tag), $part); }
                $part = preg_replace_callback('/((href|src|action|formaction|xlink:href|poster|background)\s*=\s*)(["\'])(.*?)\3/is', function($m) {
                    $attrName = strtolower($m[2]);
                    $normalized = html_entity_decode($m[4], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $normalized = preg_replace('/[\s\x00-\x1F\x7F]/', '', $normalized);
                    if (self::isDangerousUrl($normalized, $attrName)) return $m[1] . $m[3] . 'blocked:' . $m[3];
                    return $m[0];
                }, $part);
                $part = preg_replace_callback('/((href|src|action|formaction|xlink:href|poster|background)\s*=\s*)([^\s"\'<>]+)/i', function($m) {
                    $attrName = strtolower($m[2]);
                    $normalized = html_entity_decode($m[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $normalized = preg_replace('/[\s\x00-\x1F\x7F]/', '', $normalized);
                    if (self::isDangerousUrl($normalized, $attrName)) return $m[1] . 'blocked:';
                    return $m[0];
                }, $part);
            }
        }
        return implode('', $parts);
    }

    private static function isDangerousUrl($url, $attrName) {
        if (preg_match('/^(javascript|vbscript)\s*:/i', $url)) return true;
        if (preg_match('/^data\s*:/i', $url)) {
            if ($attrName === 'src' && preg_match('#^data:image/(png|jpeg|jpg|gif|webp)\s*;\s*base64,[A-Za-z0-9+/=]+$#i', $url)) return false;
            return true;
        }
        return false;
    }

    private static function sanitizeIframes($part) {
        $allowedHosts = self::allowedIframeHosts();
        return preg_replace_callback('#<iframe\b([^>]*?)(?:/\s*>|>\s*(.*?)</iframe\s*>)#is', function($m) use ($allowedHosts) {
            $attrs = $m[1]; $inner = isset($m[2]) ? $m[2] : '';
            if (!preg_match('/\bsrc\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $attrs, $sm)) return self::escapeIframe($attrs, $inner);
            $src = trim(html_entity_decode($sm[1] !== '' ? $sm[1] : ($sm[2] !== '' ? $sm[2] : $sm[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $normalized = preg_replace('/[\s\x00-\x1F\x7F]/', '', $src);
            if (!self::iframeSrcAllowed($normalized, $allowedHosts)) return self::escapeIframe($attrs, $inner);
            $safeAttrs = ' src="' . htmlspecialchars($normalized, ENT_QUOTES) . '"';
            if (preg_match('/\bwidth\s*=\s*"?(\d{1,4})"?/i', $attrs, $w)) $safeAttrs .= ' width="' . $w[1] . '"';
            if (preg_match('/\bheight\s*=\s*"?(\d{1,4})"?/i', $attrs, $h)) $safeAttrs .= ' height="' . $h[1] . '"';
            if (preg_match('/\btitle\s*=\s*"([^"<>]{0,200})"/i', $attrs, $t)) $safeAttrs .= ' title="' . htmlspecialchars($t[1], ENT_QUOTES) . '"';
            return '<iframe' . $safeAttrs . ' frameborder="0" allowfullscreen loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>';
        }, $part);
    }

    private static function escapeIframe($attrs, $inner) { return '&lt;iframe' . htmlspecialchars($attrs, ENT_QUOTES) . '&gt;' . $inner . '&lt;/iframe&gt;'; }

    private static function allowedIframeHosts() {
        if (!defined('ALLOWED_IFRAME_HOSTS')) return [];
        $hosts = [];
        foreach (explode(',', (string)ALLOWED_IFRAME_HOSTS) as $h) {
            $h = strtolower(trim($h));
            if ($h === '' || !preg_match('/^[a-z0-9.\-]+$/', $h)) continue;
            $hosts[] = $h;
        }
        return $hosts;
    }

    private static function iframeSrcAllowed($src, array $allowedHosts) {
        if (!$allowedHosts) return false;
        if (!preg_match('~^https://([^/?#]+)(?:[/?#]|$)~i', $src, $m)) return false;
        $host = strtolower($m[1]);
        if (($at = strrpos($host, '@')) !== false) $host = substr($host, $at + 1);
        if (($colon = strrpos($host, ':')) !== false) $host = substr($host, 0, $colon);
        return in_array($host, $allowedHosts, true);
    }

    public function render():string{
        $content=$this->loadContent("WEB");
        if($content!=false){$source=$content;}else{$source="# ".$this->TITLE."\n";}
        $attachments_array=$this->attachments();
        if(count($attachments_array)){
            $source.="\n\n___\n";
            foreach($attachments_array as $attachment_fe){ $source.="- [".$attachment_fe->label."](".$attachment_fe->url.")\n"; }
        }
        $sub_documents=Document::index($this->ID);
        if(count($sub_documents)){
            $source.="\n\n___\n";
            foreach($sub_documents as $sub_element_fe){
                $source.="- [".$sub_element_fe->label."](".PATH.$sub_element_fe->url.")\n";
                $sub_sub_documents=Document::index($sub_element_fe->url);
                foreach($sub_sub_documents as $sub_sub_element_fe){ $source.="\t- [".$sub_sub_element_fe->label."](".PATH.$sub_sub_element_fe->url.")\n"; }
            }
        }
        if(!$content && !count($sub_documents)){
            if(MODE=="view"){
                if($this->ID=='homepage'){
                    $source="# Welcome\nThis is your Wiki|Docs home page.\n\n";
                    if(Session::getInstance()->autenticationLevel()==2) $source.="Click the edit button to create this page!";
                }else{
                    $source="# Error 404\nWe are sorry but the page you are looking for does not exist.\n\n";
                    if(!headers_sent()) http_response_code(404);
                    if(Session::getInstance()->autenticationLevel()==2) $source.="Click the edit button to create this page!";
                }
            }
        }
        return $source;
    }

    public function images():array{
        $images_array=array();
        if(is_dir($this->DIR)){
            foreach(scandir($this->DIR) as $element_fe){
                if(is_dir($this->DIR."/".$element_fe)) continue;
                $fe=explode(".",$element_fe);
                if(!in_array(end($fe),array("png","gif","jpg","jpeg","svg"))) continue;
                $images_array[]=$element_fe;
            }
        }
        sort($images_array);
        return $images_array;
    }

    public function attachments():array{
        $attachments_array=array();
        if(is_dir($this->DIR)){
            foreach(scandir($this->DIR) as $element_fe){
                if(is_dir($this->DIR."/".$element_fe)) continue;
                $fe=explode(".",$element_fe);
                if(!in_array(end($fe),array("pdf","txt","doc","docx","xls","xlsx","ppt","pptx"))) continue;
                $a=new stdClass();
                $a->label=$element_fe;
                $a->url=PATH."datasets/documents/".$this->ID."/".rawurlencode($element_fe);
                $attachments_array[]=$a;
            }
        }
        sort($attachments_array);
        return $attachments_array;
    }

    public function versions():array{
        $versions_array=array();
        if(is_dir($this->DIR."versions/")){
            foreach(scandir($this->DIR."versions/") as $e){
                if(is_dir($this->DIR."versions/".$e)) continue;
                $fe=explode(".",$e);
                if(end($fe)!=='md') continue;
                $v=new stdClass();
                $v->label=$e;
                $v->url=$this->URL."?version=".substr($e,0,-3);
                $versions_array[]=$v;
            }
        }
        sort($versions_array);
        return $versions_array;
    }

    public function hierarchy():array{
        $hierarchy_array=array();
        $breadcrumbs=explode("/",$this->ID);
        foreach($breadcrumbs as $i=>$bc){
            $p=null;
            foreach($breadcrumbs as $j=>$bc2) if($j<=$i) $p.="/".$bc2;
            $it=new stdClass();
            $it->label=$bc;
            $it->path=substr($p,1);
            $hierarchy_array[]=$it;
        }
        return $hierarchy_array;
    }

    static function getTitle(string $document):string{
        $title='';
        $dir = DIR."datasets/documents/".$document."/";
        $content_path = self::resolveContentFile($dir);
        if($content_path && file_exists($content_path)){
            $h=fopen($content_path,"r");
            while(!feof($h)){
                $l=fgets($h);
                if(substr($l,0,2)=="# "){ $title=trim(substr($l,1)); break; }
            }
            fclose($h);
        }
        if(!strlen($title)){
            $h=explode("/",$document);
            $title=ucwords(str_replace("-"," ",end($h)));
        }
        return $title;
    }

    static function getUpdateDate(string $document):string{
        $date=0;
        $dir = DIR."datasets/documents/".$document."/";
        $files = array_merge(glob($dir.'content.md'), glob($dir.'content.*.md'));
        foreach($files as $f){ $m=filemtime($f); if($m>$date) $date=$m; }
        return $date;
    }

    static function list(?string $parent=null):array{
        $r=array();
        if(substr((string)$parent,-1)!="/") $parent.="/";
        if($parent=="/") $parent=null;
        $dp=DIR."datasets/documents/".$parent;
        if(is_dir($dp)){
            foreach(scandir($dp) as $e){
                if(in_array($e,array(".","..","versions"))) continue;
                if(!is_dir($dp.$e)) continue;
                $d=new stdClass();
                $d->id=$e; $d->path=$parent.$e; $d->url=URL.$d->path; $d->dir=$dp.$e;
                $r[]=$d;
            }
        }
        return $r;
    }

    static function index(?string $parent=null):array{
        $ia=array(); $da=array();
        $dirs=Document::list($parent);
        if(count($dirs)){
            foreach($dirs as $d){
                if($d->id=="homepage") continue;
                $du=$parent."/".$d->id;
                $dl=Document::getTitle($du);
                if(substr($du,0,1)=="/") $du=substr($du,1);
                $da[$du]=$dl;
            }
        }
        asort($da, SORT_NATURAL);
        foreach($da as $u=>$l){
            $el=new stdClass(); $el->label=$l; $el->url=$u;
            $ia[]=$el;
        }
        return $ia;
    }

    static function search(string $query,?string $parent=null):array{
        $query = html_entity_decode($query);
        $query = trim($query);
        if(empty($query)) return array();
        preg_match_all('/"([^"]+)"|(\S+)/', $query, $m);
        $qa = array_filter(array_merge($m[1], $m[2]));
        function _ta(&$a,$p=null){ foreach(Document::list($p) as $d){ $a[]=$d->path; _ta($a,$d->path); } }
        $pa=array(); $ma=array();
        _ta($pa,$parent);
        foreach($pa as $pf){
            $dir = DIR."datasets/documents/".$pf."/";
            $cfs = array_merge(glob($dir.'content.md'), glob($dir.'content.*.md'));
            foreach($cfs as $cf){
                $h=fopen($cf,"r");
                if($h){
                    while(!feof($h)){
                        $buf=fgets($h);
                        $bid=md5($buf.rand());
                        foreach($qa as $qf){
                            if(stripos($buf,$qf)!==false){
                                $buf=htmlspecialchars($buf);
                                $buf=self::highlighting($qf,$buf);
                                $ma[$pf][$bid]=$buf;
                                if(count($ma[$pf])>2) continue(4);
                            }
                        }
                    }
                    fclose($h);
                }
            }
        }
        return $ma;
    }

    private static function highlighting($s,$str){
        $i=0; $r="";
        while($i<strlen($str)){
            if(strtolower(substr($str,$i,strlen($s)))===strtolower($s)){ $r.="<mark>".substr($str,$i,strlen($s))."</mark>"; $i+=strlen($s); }
            else{ $r.=substr($str,$i,1); $i++; }
        }
        return $r;
    }

    public static function getLastEditedDocs($limit = 7) {
        $dd = realpath(dirname(__FILE__)) . '/../datasets/documents/';
        $docs = [];
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dd));
        foreach ($it as $f) {
            if ($f->isFile() && preg_match('/^content(\.[a-z-]+)?\.md$/i', $f->getFilename())) {
                $rp = rtrim(str_replace($dd, '', $f->getPath()), '/');
                if ($rp === 'homepage') continue;
                $found = false;
                foreach ($docs as $k => &$d) {
                    if ($d['path'] === $rp) { if ($f->getMTime() > $d['timestamp']) $d['timestamp'] = $f->getMTime(); $found = true; break; }
                }
                if (!$found) $docs[] = ['path' => $rp, 'timestamp' => $f->getMTime()];
            }
        }
        usort($docs, function ($a, $b) { return $b['timestamp'] - $a['timestamp']; });
        return array_slice($docs, 0, $limit);
    }

    public static function getTotalContentCount() {
        $dd = realpath(dirname(__FILE__)) . '/../datasets/documents/';
        $dirs = [];
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dd));
        foreach ($it as $f) {
            if ($f->isFile() && preg_match('/^content(\.[a-z-]+)?\.md$/i', $f->getFilename())) {
                $rd = rtrim(str_replace($dd, '', $f->getPath()), '/');
                if ($rd !== 'homepage') $dirs[$rd] = true;
            }
        }
        return count($dirs);
    }
}