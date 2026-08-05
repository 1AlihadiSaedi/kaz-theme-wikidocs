<?php
/**
 * Settings — Modern UI
 */
require_once('bootstrap.inc.php');
$TXT = Localization::getInstance();

if (Session::getInstance()->autenticationLevel() != 2) { wdf_alert($TXT->SubmitNotAuthenticated,'danger'); wdf_redirect(PATH); }

$g_act = ($_GET['act'] ?? '');
if ($g_act == 'store') {
  if (!wdf_csrf_check()) { wdf_alert('CSRF protection triggered!','danger'); wdf_redirect(PATH); }
  $EDITCODE = ($_POST['editcode'] === EDITCODE ? EDITCODE : password_hash($_POST['editcode'], PASSWORD_DEFAULT));
  $VIEWCODE = ($_POST['viewcode'] === VIEWCODE ? VIEWCODE : (strlen($_POST['viewcode']) ? password_hash($_POST['viewcode'], PASSWORD_DEFAULT) : null));
  $EDITCODE = str_replace('$','\\$',$EDITCODE);
  if ($VIEWCODE != null) $VIEWCODE = str_replace('$','\\$',$VIEWCODE);
  $config = "<?php\n";
  $config .= "define('DEBUGGABLE',".(DEBUGGABLE?'true':'false').");\n";
  $config .= "define('PATH',\"".PATH."\");\n";
  $config .= "defined('LANG') || define('LANG',\"".$_POST['lang']."\");\n";
  $config .= "define('TIMEZONE',".($_POST['timezone']?'"'.$_POST['timezone'].'"':'null').");\n";
  $config .= "define('TITLE',\"".$_POST['title']."\");\n";
  $config .= "define('SUBTITLE',\"".$_POST['subtitle']."\");\n";
  $config .= "define('OWNER',\"".$_POST['owner']."\");\n";
  $config .= "define('NOTICE',".($_POST['notice']?'"'.$_POST['notice'].'"':'null').");\n";
  $config .= "define('PRIVACY',".($_POST['privacy']?'"'.$_POST['privacy'].'"':'null').");\n";
  $config .= "define('EDITCODE',\"".$EDITCODE."\");\n";
  $config .= "define('VIEWCODE',".($VIEWCODE?'"'.$VIEWCODE.'"':'null').");\n";
  $config .= "define('COLOR',\"".$_POST['color']."\");\n";
  $config .= "define('DARK',".(isset($_POST['dark'])?'true':'false').");\n";
  $config .= "define('GTAG',".($_POST['gtag']?'"'.$_POST['gtag'].'"':'null').");\n";
  file_put_contents(BASE.'datasets/config.inc.php',$config);
  wdf_alert($TXT->SettingsStored,'success');
  wdf_redirect(PATH);
}
?><!DOCTYPE html>
<html lang="en" data-theme="<?= DARK?'dark':'light' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="<?= COLOR ?>">

  <!-- Anti-FOWT: apply saved theme before any CSS loads -->
  <script>(function(){var t=localStorage.getItem('wikidocs-theme');if(t)document.documentElement.setAttribute('data-theme',t);})()</script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="helpers/font-awesome-4.7.0/css/font-awesome.min.css">
  <style>:root{--color-primary:<?= COLOR ?>;--color-primary-rgb:<?= implode(',',sscanf(COLOR,"#%02x%02x%02x")) ?>;}</style>
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="icon" type="image/x-icon" href="favicon.ico" sizes="any">
  <title><?= $TXT->Settings ?> — Wiki|Docs</title>
</head>
<body>

<svg class="svg-sprite" xmlns="http://www.w3.org/2000/svg">
  <symbol id="icon-settings" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></symbol>
  <symbol id="icon-lock" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
  <symbol id="icon-globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></symbol>
  <symbol id="icon-moon" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></symbol>
  <symbol id="icon-pencil" viewBox="0 0 24 24"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></symbol>
  <symbol id="icon-save" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></symbol>
  <symbol id="icon-arrow-left" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></symbol>
</svg>

<div class="settings-page">
  <h1><svg class="svg-icon svg-icon-xl" aria-hidden="true"><use href="#icon-settings"/></svg> Wiki|Docs</h1>
  <p class="subtitle"><?= $TXT->Payoff ?></p>

  <form action="settings.php?act=store" method="post">
    <input type="hidden" name="token" value="<?= Session::getInstance()->token() ?>">
    <div class="settings-section">
      <h2><svg class="svg-icon svg-icon-lg" aria-hidden="true"><use href="#icon-pencil"/></svg> <?= $TXT->Settings ?></h2>
      <p class="text-secondary" style="margin-bottom:var(--spacing-lg)"><?= $TXT->SettingsConfigure ?>…</p>
      <div class="settings-grid">
        <div class="form-group"><label class="form-label" for="title"><?= $TXT->SettingsTitle ?></label><input type="text" id="title" name="title" class="form-input" value="<?= TITLE ?>" required></div>
        <div class="form-group"><label class="form-label" for="subtitle"><?= $TXT->SettingsSubtitle ?></label><input type="text" id="subtitle" name="subtitle" class="form-input" value="<?= SUBTITLE ?>" required></div>
        <div class="form-group"><label class="form-label" for="owner"><?= $TXT->SettingsOwner ?></label><input type="text" id="owner" name="owner" class="form-input" value="<?= OWNER ?>" placeholder="<?= $TXT->SettingsOwnerPlaceholder ?>" required></div>
        <div class="form-group"><label class="form-label" for="notice"><?= $TXT->SettingsNotice ?></label><input type="text" id="notice" name="notice" class="form-input" value="<?= NOTICE ?>" placeholder="<?= $TXT->SettingsNoticePlaceholder ?>"></div>
        <div class="form-group full-width"><label class="form-label" for="privacy"><?= $TXT->SettingsPrivacy ?></label><input type="text" id="privacy" name="privacy" class="form-input" value="<?= PRIVACY ?>" placeholder="<?= $TXT->SettingsPrivacyPlaceholder ?>"></div>
      </div>
    </div>
    <div class="settings-section">
      <h2><svg class="svg-icon svg-icon-lg" aria-hidden="true"><use href="#icon-lock"/></svg> Access Control</h2>
      <div class="settings-grid">
        <div class="form-group"><label class="form-label" for="editcode"><?= $TXT->SettingsEditCode ?></label><input type="password" id="editcode" name="editcode" class="form-input" value="<?= EDITCODE ?>" placeholder="<?= $TXT->SettingsEditCodePlaceholder ?>…" required></div>
        <div class="form-group"><label class="form-label" for="viewcode"><?= $TXT->SettingsViewCode ?></label><input type="password" id="viewcode" name="viewcode" class="form-input" value="<?= VIEWCODE ?>" placeholder="<?= $TXT->SettingsViewCodePlaceholder ?>…"></div>
      </div>
    </div>
    <div class="settings-section">
      <h2><svg class="svg-icon svg-icon-lg" aria-hidden="true"><use href="#icon-settings"/></svg> Appearance</h2>
      <div class="settings-grid">
        <div class="form-group"><label class="form-label" for="color"><?= $TXT->SettingsColor ?></label><div style="display:flex;gap:8px;align-items:center"><input type="color" id="color-picker" value="<?= COLOR ?>" style="width:42px;height:42px;border:none;border-radius:8px;cursor:pointer;padding:0"><input type="text" id="color" name="color" class="form-input" value="<?= COLOR ?>" placeholder="#4CAF50" required style="flex:1"></div></div>
        <div class="form-group"><label class="form-label" for="lang"><svg class="svg-icon svg-icon-sm" aria-hidden="true"><use href="#icon-globe"/></svg> <?= $TXT->SettingsLanguage ?></label><select id="lang" name="lang" class="form-input"><?php foreach(Localization::available() as $v=>$l): ?><option value="<?= $v ?>" <?= ($v==LANG?'selected':'') ?>><?= $l ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label class="form-label" for="timezone"><?= $TXT->SettingsTimezone ?></label><select id="timezone" name="timezone" class="form-input"><option value="">Default</option><?php foreach(DateTimeZone::listIdentifiers() as $tz): ?><option value="<?= $tz ?>" <?= ($tz==TIMEZONE?'selected':'') ?>><?= $tz ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label class="form-label" for="gtag"><?= $TXT->SettingsGtag ?></label><input type="text" id="gtag" name="gtag" class="form-input" value="<?= GTAG ?>" placeholder="UA-123456789-1"></div>
        <div class="form-group full-width"><label class="checkbox-group"><input type="checkbox" name="dark" id="check-dark" <?= DARK?'checked':'' ?>><span><svg class="svg-icon svg-icon-sm" aria-hidden="true"><use href="#icon-moon"/></svg> <?= $TXT->SettingsDark ?></span></label></div>
      </div>
    </div>
    <div class="settings-actions">
      <a href="<?= PATH ?>" class="btn btn-secondary"><svg class="svg-icon svg-icon-sm" aria-hidden="true"><use href="#icon-arrow-left"/></svg> <?= $TXT->SettingsCancel ?></a>
      <button type="submit" class="btn btn-primary"><svg class="svg-icon svg-icon-sm" aria-hidden="true"><use href="#icon-save"/></svg> <?= $TXT->SettingsSubmit ?></button>
    </div>
  </form>
</div>

<script>document.getElementById('color-picker').addEventListener('input',function(){document.getElementById('color').value=this.value});document.getElementById('color').addEventListener('input',function(){document.getElementById('color-picker').value=this.value});</script>
<div class="toast-container" id="toast-container"></div>
<script>function showToast(m,t){var c=document.getElementById('toast-container');if(!c)return;var d=document.createElement('div');d.className='toast toast-'+t;d.innerHTML=m;c.appendChild(d);setTimeout(function(){if(d.parentNode)d.remove()},4000)}</script>
</body>
</html>