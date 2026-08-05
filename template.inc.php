<?php
if (isset($_GET['set_lang'])) {
  $nl = preg_replace('/[^a-z-]/', '', $_GET['set_lang']);
  setcookie('wikidocs_lang', $nl, time() + 60*60*24*365, '/');
  $did = DOC;
  $ps = explode('/', $did);
  $ls = array_pop($ps);
  $kl = array_keys(Localization::available());
  foreach ($kl as $l) { if (strpos($ls, $l . '.') === 0) { $ls = substr($ls, strlen($l) + 1); break; } }
  if ($nl !== 'en') { $ls = $nl . '.' . $ls; }
  $ps[] = $ls;
  $did = implode('/', $ps);
  header('Location: ' . URL . $did);
  exit;
}
$cl = LANG;
if (isset($_COOKIE['wikidocs_lang'])) {
  $t = preg_replace('/[^a-z-]/', '', $_COOKIE['wikidocs_lang']);
  if (file_exists(BASE . 'localizations/' . $t . '.json')) { $cl = $t; }
}
$al = Localization::available();
?><!DOCTYPE html>
<html lang="<?= $cl ?>" data-theme="<?= $APP->DARK ? 'dark' : 'light' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>(function(){var t=localStorage.getItem('wikidocs-theme');if(t)document.documentElement.setAttribute('data-theme',t);})()</script>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $APP->PATH ?>helpers/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?= $APP->PATH ?>helpers/easymde-2.16.1/css/easymde.min.css">
<link rel="stylesheet" href="<?= $APP->PATH ?>helpers/highlightjs-11.9.0/css/<?= ($APP->DARK ? 'monokai-sublime' : 'default') ?>.min.css">
<link rel="stylesheet" href="<?= $APP->PATH ?>helpers/katex-0.16.10/katex.min.css">
<style>:root{--color-primary:<?= $APP->COLOR ?>;--color-primary-rgb:<?= implode(',',sscanf($APP->COLOR,'#%02x%02x%02x')) ?>;}</style>
<link rel="stylesheet" href="<?= $APP->PATH ?>styles/styles.css">
<?php if(file_exists($APP->DIR.'styles/styles-custom.css')): ?><link rel="stylesheet" href="<?= $APP->PATH ?>styles/styles-custom.css"><?php endif; ?>
<link rel="icon" type="image/x-icon" href="<?= $APP->PATH ?>favicon.ico" sizes="any">
<title><?= ($DOC->ID!='homepage'?$DOC->TITLE.' - ':'').$APP->TITLE ?></title>
<?php if(strlen(GTAG ?? '') && Session::getInstance()->privacyAgreeded()): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= GTAG ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= GTAG ?>');</script>
<?php endif; ?>
</head>
<body>
<svg class="svg-sprite" xmlns="http://www.w3.org/2000/svg">
<symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></symbol>
<symbol id="i-menu" viewBox="0 0 24 24"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></symbol>
<symbol id="i-print" viewBox="0 0 24 24"><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/></symbol>
<symbol id="i-lock" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
<symbol id="i-unlock" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></symbol>
<symbol id="i-close" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
<symbol id="i-trash" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></symbol>
<symbol id="i-history" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></symbol>
<symbol id="i-save" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></symbol>
<symbol id="i-image" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></symbol>
<symbol id="i-attach" viewBox="0 0 24 24"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></symbol>
<symbol id="i-list" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></symbol>
<symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></symbol>
<symbol id="i-moon" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></symbol>
<symbol id="i-globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></symbol>
<symbol id="i-arrow-up" viewBox="0 0 24 24"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></symbol>
<symbol id="i-copy" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></symbol>
<symbol id="i-file" viewBox="0 0 24 24"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></symbol>
<symbol id="i-check-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></symbol>
<symbol id="i-warning" viewBox="0 0 24 24"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></symbol>
<symbol id="i-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></symbol>
<symbol id="i-danger" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></symbol>
<symbol id="i-check-square" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></symbol>
<symbol id="i-undo" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></symbol>
<symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></symbol>
<symbol id="i-new-doc" viewBox="0 0 24 24"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></symbol>
<symbol id="i-pencil" viewBox="0 0 24 24"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></symbol>
<symbol id="i-settings" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></symbol>
</svg>

<div class="app-layout">
<div class="sidebar-overlay" id="sidebar-overlay"></div>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand"><h1><?= $APP->TITLE ?></h1><span><?= $APP->SUBTITLE ?></span></div>
  <div class="sidebar-search"><form action="<?= $APP->PATH ?>" method="get" autocomplete="off"><div class="search-wrapper"><input type="text" id="search" name="search" placeholder="<?= $TXT->Search ?>…" value="<?= SEARCH ?>"><svg class="svg-icon search-icon"><use href="#i-search"/></svg></div></form></div>
  <nav class="sidebar-nav" id="sidebar-nav">
    <?php if(in_array(MODE,array('view','edit','search'))): $idx=Document::index(); foreach($idx as $fe): $isAct=($fe->url==substr($DOC->ID,0,strlen($fe->url))); ?>
      <a class="nav-item <?= $isAct?'active':'' ?>" href="<?= $APP->PATH.$fe->url ?>"><?= $fe->label ?></a>
      <?php if($isAct): $sub=Document::index($fe->url); foreach($sub as $sf): $sAct=($sf->url==substr($DOC->ID,0,strlen($sf->url))); ?>
        <div class="nav-sub"><a class="nav-item <?= $sAct?'active':'' ?>" href="<?= $APP->PATH.$sf->url ?>"><?= $sf->label ?></a>
        <?php if($sAct): $sub2=Document::index($sf->url); foreach($sub2 as $s2f): ?>
          <div class="nav-sub-sub"><a class="nav-item <?= ($s2f->url==$DOC->ID?'active':'') ?>" href="<?= $APP->PATH.$s2f->url ?>"><?= $s2f->label ?></a></div>
        <?php endforeach; endif; ?></div>
      <?php endforeach; endif; ?>
    <?php endforeach; endif; ?>
  </nav>
  <div class="sidebar-footer"><span class="footer-title"><?= $APP->OWNER ?></span><span class="footer-desc"><?= $APP->NOTICE ?></span></div>
</aside>

<main class="main-content">
<header class="topbar">
  <div class="topbar-left"><button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Menu"><svg class="svg-icon svg-icon-lg"><use href="#i-menu"/></svg></button>
    <nav class="breadcrumb">
      <?php if($DOC->ID=='homepage'): ?><span class="current"><?= $APP->TITLE ?></span>
      <?php else: ?><a href="<?= PATH ?>"><?= $APP->TITLE ?></a>
        <?php foreach($DOC->hierarchy() as $el): ?><span class="separator">/</span>
          <?php if($DOC->ID==$el->path): ?><span class="current"><?= $el->label ?></span>
          <?php else: ?><a href="<?= $APP->PATH.$el->path ?>"><?= $el->label ?></a><?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </nav>
  </div>

  <div class="topbar-right" id="topbar-actions">
    <div class="lang-switcher" id="lang-switcher">
      <button class="lang-switcher-btn" id="lang-switcher-btn" aria-label="Language"><?= strtoupper(substr($cl,0,2)) ?></button>
      <div class="lang-switcher-dropdown" id="lang-dropdown">
        <?php foreach($al as $code => $name): ?>
          <a href="?set_lang=<?= $code ?>" class="<?= $code==$cl?'active':'' ?>"><span class="lang-code"><?= strtoupper(substr($code,0,2)) ?></span><?= $name ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme"><svg class="svg-icon icon-sun"><use href="#i-sun"/></svg><svg class="svg-icon icon-moon"><use href="#i-moon"/></svg></button>

    <?php if(MODE=='view'): ?>
      <a class="btn-icon" href="<?= $DOC->URL ?>?print" target="_blank" data-tooltip="<?= $TXT->TooltipPrint ?>"><svg class="svg-icon"><use href="#i-print"/></svg></a>
      <?php if(Session::getInstance()->autenticationLevel()==2): ?>
        <a class="btn-icon" href="<?= $APP->PATH ?>settings.php" data-tooltip="<?= $TXT->TooltipSettings ?>"><svg class="svg-icon"><use href="#i-settings"/></svg></a>
        <button class="btn-icon" data-tooltip="<?= $TXT->TooltipNewDocument ?>" onclick="newDocument()"><svg class="svg-icon"><use href="#i-new-doc"/></svg></button>
        <a class="btn btn-primary btn-sm" href="<?= $DOC->URL ?>?edit"><svg class="svg-icon svg-icon-sm"><use href="#i-pencil"/></svg> <span class="btn-label"><?= $TXT->TooltipEditDocument ?></span></a>
        <?php if($DOC->VERSION!=='latest'): ?>
          <form method="post" action="<?= $APP->PATH ?>submit.php?act=content_restore" style="display:inline" onsubmit="return confirm('<?= str_replace(array("'",'"'),"\\'",$TXT->TooltipRestoreVersionConfirm) ?>')">
            <input type="hidden" name="token" value="<?= htmlspecialchars(Session::getInstance()->token(),ENT_QUOTES) ?>">
            <input type="hidden" name="document" value="<?= htmlspecialchars($DOC->ID,ENT_QUOTES) ?>">
            <input type="hidden" name="version" value="<?= htmlspecialchars($DOC->VERSION,ENT_QUOTES) ?>">
            <button type="submit" class="btn btn-warning btn-sm" data-tooltip="<?= $TXT->TooltipRestoreVersion ?>"><svg class="svg-icon svg-icon-sm"><use href="#i-undo"/></svg> <span class="btn-label"><?= $TXT->TooltipRestoreVersion ?></span></button>
          </form>
        <?php endif; ?>
      <?php else: ?>
        <a class="btn btn-primary btn-sm" href="<?= $DOC->URL ?>?auth"><svg class="svg-icon svg-icon-sm"><use href="#i-unlock"/></svg> <span class="btn-label"><?= $TXT->TooltipSignIn ?></span></a>
      <?php endif; ?>
    <?php endif; ?>

    <?php if(MODE=='edit'): ?>
      <button class="btn btn-ghost btn-sm" onclick="window.location.href='<?= $DOC->URL ?>'"><svg class="svg-icon svg-icon-sm"><use href="#i-close"/></svg> <span class="btn-label"><?= $TXT->TooltipCancelEditing ?></span></button>
      <button class="btn btn-secondary btn-sm modal-trigger" data-modal="modal-images"><svg class="svg-icon svg-icon-sm"><use href="#i-image"/></svg> <span class="btn-label"><?= $TXT->Images ?></span></button>
      <button class="btn btn-secondary btn-sm modal-trigger" data-modal="modal-attachments"><svg class="svg-icon svg-icon-sm"><use href="#i-attach"/></svg> <span class="btn-label"><?= $TXT->Attachments ?></span></button>
      <form method="post" action="<?= $APP->PATH ?>submit.php?act=content_delete" style="display:inline" onsubmit="return confirm('<?= str_replace(array("'",'"'),"\\'",$TXT->TooltipDeleteDocumentConfirm) ?>')">
        <input type="hidden" name="token" value="<?= htmlspecialchars(Session::getInstance()->token(),ENT_QUOTES) ?>">
        <input type="hidden" name="document" value="<?= htmlspecialchars($DOC->ID,ENT_QUOTES) ?>">
        <button type="submit" class="btn btn-danger btn-sm"><svg class="svg-icon svg-icon-sm"><use href="#i-trash"/></svg> <span class="btn-label"><?= $TXT->TooltipDeleteDocument ?></span></button>
      </form>
      <button class="btn btn-secondary btn-sm modal-trigger" data-modal="modal-versions"><svg class="svg-icon svg-icon-sm"><use href="#i-list"/></svg> <span class="btn-label"><?= $TXT->TooltipVersions ?></span></button>
      <button id="editor-revision" class="btn btn-warning btn-sm" data-tooltip="<?= $TXT->TooltipVersioning ?>"><svg class="svg-icon svg-icon-sm" id="editor-revision-icon-svg"><use href="#i-check-square"/></svg> <span class="btn-label"><?= $TXT->TooltipVersioning ?></span></button>
      <button id="editor-save" class="btn btn-success btn-sm"><svg class="svg-icon svg-icon-sm"><use href="#i-save"/></svg> <span class="btn-label"><?= $TXT->TooltipSave ?></span></button>
    <?php endif; ?>
  </div>
</header>

<div class="content-container">

<?php if(MODE=='view'): ?>
  <article><?= $PARSER->text(parseInlineText($DOC->render())) ?></article>
<?php endif; ?>

<?php if(MODE=='auth'): ?>
  <div class="auth-form"><form method="post" action="<?= $APP->PATH ?>submit.php?act=authentication">
    <input type="hidden" name="token" value="<?= Session::getInstance()->token() ?>"><input type="hidden" name="document" value="<?= $DOC->ID ?>">
    <h2><svg class="svg-icon svg-icon-lg"><use href="#i-lock"/></svg> <?= $TXT->AuthPassword ?></h2>
    <p class="text-center text-secondary" style="margin-bottom:var(--spacing-lg)"><?= $APP->TITLE ?></p>
    <div class="form-group"><label class="form-label" for="password"><?= $TXT->AuthPassword ?></label><div style="display:flex;gap:8px"><input type="password" id="password" name="password" class="form-input" required autofocus placeholder="••••••••"><button type="submit" class="btn btn-primary"><?= $TXT->AuthSubmit ?></button></div></div>
  </form></div>
<?php endif; ?>

<?php if(MODE=='edit'): ?>
  <form id="editor-form" method="post" action="<?= $APP->PATH ?>submit.php?act=content_save">
    <input type="hidden" name="token" value="<?= Session::getInstance()->token() ?>"><input type="hidden" name="revision" value="1"><input type="hidden" name="document" value="<?= $DOC->ID ?>">
    <?php $src=null;if(isset($_GET['draft'])&&file_exists($DOC->DIR.'draft.md'))$src=file_get_contents($DOC->DIR.'draft.md');if(!strlen($src ?? ''))$src=$DOC->loadContent(); ?>
    <textarea id="simplemde" name="content"><?= htmlspecialchars(strlen($src)?$src:'# '.$DOC->TITLE) ?></textarea>
  </form>
<?php endif; ?>

<?php if(MODE=='search'): ?>
  <article class="search-results"><h1><svg class="svg-icon svg-icon-xl"><use href="#i-search"/></svg> <?= $TXT->SearchResults ?></h1>
    <?php $ma=Document::search(SEARCH);if(count($ma)):foreach($ma as $df=>$mf): ?>
      <div class="search-result"><h3><a href="<?= URL.$df ?>"><?= $df ?></a></h3><?php foreach($mf as $m): ?><p><?= $m ?></p><?php endforeach; ?></div>
    <?php endforeach;else: ?>
      <div class="search-no-results"><p style="font-size:3rem;margin-bottom:8px"><svg class="svg-icon" style="width:48px;height:48px;stroke-width:1.5"><use href="#i-search"/></svg></p><p><?= $TXT->SearchNoResults ?> <strong><?= SEARCH ?></strong></p></div>
    <?php endif; ?>
  </article>
<?php endif; ?>

<footer class="content-footer">
  <span><svg class="svg-icon svg-icon-sm"><use href="#i-history"/></svg> <?= $TXT->LastUpdate ?> <?= wdf_timestamp_format($DOC->TIMESTAMP,'Y-m-d H:i') ?></span>
  <span><?= $TXT->PoweredBy ?> <a href="https://github.com/Zavy86/WikiDocs" target="_blank" rel="noopener">Wiki|Docs</a><?php if($APP->DEBUG) echo ' '.$APP->VERSION; ?><?php if(Session::getInstance()->isAuthenticated()): ?> · <a href="<?= $DOC->URL ?>?exit"><svg class="svg-icon svg-icon-sm"><use href="#i-logout"/></svg> <?= $TXT->Logout ?></a><?php endif; ?></span>
</footer>
<?php if($APP->DEBUG): ?><section class="debug-panel"><?= wdf_dump($DOC,'DOCUMENT').wdf_dump($APP,'APPLICATION') ?></section><?php endif; ?>
</div>
</main>
</div>

<button class="back-to-top" id="back-to-top" aria-label="Back to top"><svg class="svg-icon"><use href="#i-arrow-up"/></svg></button>

<?php if(!Session::getInstance()->privacyAgreeded()): ?>
<div class="modal-overlay active" id="modal-privacy"><div class="modal privacy-modal"><div class="modal-header"><h3><svg class="svg-icon svg-icon-lg"><use href="#i-info"/></svg> <?= $TXT->CookieAgreement ?></h3></div><div class="modal-body"><p><?= PRIVACY ?></p></div><div class="modal-footer"><a href="https://duckduckgo.com/" class="btn btn-secondary btn-sm"><?= $TXT->CookieButtonDisagree ?></a><a href="?privacy=1" class="btn btn-primary btn-sm"><?= $TXT->CookieButtonAgree ?></a></div></div></div>
<?php endif; ?>

<div class="toast-container" id="toast-container"></div>

<script>var APP=<?= json_encode($APP->export()) ?>;var DOC=<?= json_encode($DOC->export()) ?>;</script>
<script src="<?= $APP->PATH ?>helpers/jquery-3.7.0/js/jquery.min.js"></script>
<script src="<?= $APP->PATH ?>helpers/highlightjs-11.9.0/js/highlight.min.js"></script>
<script src="<?= $APP->PATH ?>helpers/katex-0.16.10/katex.min.js"></script>
<script src="<?= $APP->PATH ?>helpers/katex-0.16.10/contrib/auto-render.min.js"></script>
<script>renderMathInElement(document.body,{delimiters:[{left:'$$',right:'$$',display:true},{left:'$',right:'$',display:false}],throwOnError:false,trust:false,strict:true,maxSize:500,maxExpand:100});hljs.highlightAll();</script>
<script src="<?= $APP->PATH ?>helpers/mermaid-9.4.3/mermaid.min.js"></script>
<script>mermaid.initialize({startOnLoad:true,theme:'<?= $APP->DARK?"dark":"neutral"?>'});</script>
<script src="<?= $APP->PATH ?>scripts/app.js"></script>
<script src="<?= $APP->PATH ?>scripts/editor-shortcuts.js"></script>
<?php if(MODE=='edit'): ?>
<script src="<?= $APP->PATH ?>helpers/easymde-2.16.1/js/easymde.min.js"></script>
<script src="<?= $APP->PATH ?>scripts/editor.js"></script>
<script>var confirm_image_delete="<?= str_replace(array("'",'"'),"\\'",$TXT->ImageDeleteConfirm) ?>";</script>
<?php endif; ?>
<script>
function copyCode(b){var c=b.nextElementSibling.textContent.trim();navigator.clipboard.writeText(c).then(function(){b.textContent='Copied!';setTimeout(function(){b.textContent='Copy'},2000)}).catch(function(){var t=document.createElement('textarea');t.value=c;document.body.appendChild(t);t.select();document.execCommand('copy');document.body.removeChild(t);b.textContent='Copied!';setTimeout(function(){b.textContent='Copy'},2000)})}
function newDocument(){var p=prompt("<?= str_replace(array("'",'"'),"\\'",$TXT->PromptNewDocument) ?>",DOC.ID+'/');if(p!==DOC.ID+'/'){p=p.replace(/\s+/g,'-').toLowerCase()+'?edit';window.location.href=APP.URL+p}}
</script>

<?php if(isset($_SESSION['wikidocs']['alerts'])): foreach($_SESSION['wikidocs']['alerts'] as $i => $a): $ct = isset($a->class) && in_array($a->class, ['success','warning','danger']) ? $a->class : 'info'; unset($_SESSION['wikidocs']['alerts'][$i]); ?>
<script>showToast("<?= addslashes($a->message) ?>","<?= $ct ?>")</script>
<?php endforeach; endif; ?>

</body>
</html>
