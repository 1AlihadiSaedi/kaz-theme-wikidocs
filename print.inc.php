<?php
/**
 * Print Template — Modern UI
 *
 * @package WikiDocs
 * @repository https://github.com/Zavy86/wikidocs
 *
 * @var WikiDocs $APP
 * @var Document $DOC
 * @var ParsedownExtra $PARSER
 */
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= ($DOC->ID != 'homepage' ? $DOC->TITLE . ' - ' : '') . $APP->TITLE ?></title>

  <link rel="stylesheet" href="<?= $APP->PATH ?>helpers/katex-0.16.10/katex.min.css">

  <style>
    :root {
      --text: #1a1a1a;
      --text-secondary: #555;
      --border: #ddd;
      --code-bg: #f5f5f5;
      --accent: <?= $APP->COLOR ?>;
    }
    @media print { :root { --text: #000; } }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
      font-size: 12pt;
      line-height: 1.7;
      color: var(--text);
      max-width: 800px;
      margin: 0 auto;
      padding: 2rem;
      -webkit-font-smoothing: antialiased;
    }
    h1, h2, h3, h4, h5, h6 {
      font-weight: 700;
      line-height: 1.3;
      color: var(--text);
      margin: 1.5em 0 0.5em;
      page-break-after: avoid;
    }
    h1 { font-size: 1.8rem; border-bottom: 2px solid var(--accent); padding-bottom: 0.3em; }
    h2 { font-size: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.2em; }
    h3 { font-size: 1.25rem; }
    h4 { font-size: 1.1rem; }
    h5, h6 { font-size: 1rem; }
    p { margin: 0.8em 0; word-wrap: break-word; }
    a { color: var(--accent); }
    strong { font-weight: 600; }
    em { font-style: italic; }
    ul, ol { padding-left: 1.5rem; margin: 0.8em 0; }
    li { margin: 0.3em 0; }
    code, pre, blockquote, .mono {
      font-family: 'JetBrains Mono', 'Fira Code', Consolas, 'Courier New', monospace;
      font-size: 10pt;
    }
    code { background: var(--code-bg); padding: 2px 6px; border-radius: 3px; border: 1px solid var(--border); }
    pre {
      background: var(--code-bg); border: 1px solid var(--border); border-radius: 6px;
      padding: 1rem; overflow-x: auto; margin: 1em 0; page-break-inside: avoid;
    }
    pre code { background: none; border: none; padding: 0; }
    blockquote {
      margin: 1em 0; padding: 0.8em 1em; border-left: 4px solid var(--accent);
      background: rgba(0,0,0,0.02); border-radius: 0 4px 4px 0;
    }
    table { width: 100%; border-collapse: collapse; margin: 1em 0; page-break-inside: avoid; }
    th, td { padding: 8px 12px; border: 1px solid var(--border); text-align: left; }
    th { background: #f9f9f9; font-weight: 600; }
    hr { border: none; border-top: 1px solid var(--border); margin: 2em 0; }
    img { max-width: 100%; height: auto; display: block; margin: 1em auto; page-break-inside: avoid; }
    iframe, .video-responsive { display: none; }
    details { page-break-inside: avoid; }
    h1, h2, h3, h4, h5, h6, p, pre, th, td { unicode-bidi: plaintext; text-align: start; }
    @media print {
      body { padding: 0; }
      pre, blockquote, table, img { page-break-inside: avoid; }
      h1, h2, h3 { page-break-after: avoid; }
      @page { margin: 2cm; }
    }
  </style>
  <script>
    window.addEventListener('beforeprint', function () {
      document.querySelectorAll('details').forEach(function (d) { d.setAttribute('open', ''); });
    });
  </script>
</head>
<body>
<script src="<?= $APP->PATH ?>helpers/mermaid-9.4.3/mermaid.min.js"></script>
<script>mermaid.initialize({ startOnLoad: true, theme: 'neutral' });</script>
<?= $PARSER->text($DOC->loadContent()) . "\n" ?>
<script src="<?= $APP->PATH ?>helpers/katex-0.16.10/katex.min.js"></script>
<script src="<?= $APP->PATH ?>helpers/katex-0.16.10/contrib/auto-render.min.js"></script>
<script>renderMathInElement(document.body);</script>
<script>window.print();</script>
</body>
</html>
