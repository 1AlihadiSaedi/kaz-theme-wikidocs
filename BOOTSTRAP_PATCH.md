# Bootstrap Patch — Language Cookie + Path-Based Switching

## What this does

When you click a language in the dropdown, the system:
1. Sets a cookie (`wikidocs_lang`)
2. Strips any existing language prefix from the current document (e.g. `fa.home` → `home`)
3. Applies the new language prefix (e.g. `home` → `en.home` or `fa.home`)
4. Redirects to the new document path

## Step 1: Patch `bootstrap.inc.php` (REQUIRED)

This step is needed so the UI text also changes language.

Find these lines (around line 63-65):

```php
// check for configuration file
if(!file_exists(realpath(dirname(__FILE__))."/datasets/config.inc.php")){header("location:setup.php");}

// include configuration file
require_once("datasets/config.inc.php");
```

**Replace with:**

```php
// check for configuration file
if(!file_exists(realpath(dirname(__FILE__))."/datasets/config.inc.php")){header("location:setup.php");}

// Language override via cookie (set by language switcher)
if (isset($_COOKIE['wikidocs_lang'])) {
  $cookieLang = preg_replace('/[^a-z-]/', '', $_COOKIE['wikidocs_lang']);
  if (file_exists(BASE . 'localizations/' . $cookieLang . '.json')) {
    define('LANG', $cookieLang);
  }
}

// include configuration file
require_once("datasets/config.inc.php");
```

## Step 2: Patch `settings.php` config generation

In `settings.php`, find this line in the config generation section:

```php
$config .= "define('LANG',\"" . $_POST['lang'] . "\");\n";
```

**Replace with:**

```php
$config .= "defined('LANG') || define('LANG',\"" . $_POST['lang'] . "\");\n";
```

(The updated settings.php in this theme already has this fix.)

## How path-based switching works

| Current URL | Switch to FA | Switch to EN |
|---|---|---|
| `/home` | → `/fa.home` | → `/home` (no prefix for EN) |
| `/fa.home` | stays `/fa.home` | → `/home` |
| `/test/samples` | → `/test/fa.samples` | → `/test/samples` |
| `/test/en.samples` | → `/test/fa.samples` | → `/test/samples` |

## Setting up content per language

Create separate document directories for each language:

```
datasets/documents/
  home/          ← default language (EN)
    content.md
  fa.home/       ← Persian version
    content.md
  test/
    samples/     ← default (EN)
      content.md
    fa.samples/  ← Persian version
      content.md
```

Or use a single directory with `content.{lang}.md` files (if using the multi-language Document.class.php from this theme):

```
datasets/documents/
  home/
    content.md       ← default
    content.fa.md    ← Persian
    content.de.md    ← German
  test/samples/
    content.md
    content.fa.md
```