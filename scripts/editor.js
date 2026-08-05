/**
 * WikiDocs — Editor Script
 */

function insertTOC(editor) {
  var cm = editor.codemirror;
  cm.replaceRange('[toc]\n', cm.getCursor());
}

function addSubscript(editor) {
  var cm = editor.codemirror;
  var s = cm.getSelection();
  if (s) { cm.replaceSelection('~' + s + '~'); }
  else { var c = cm.getCursor(); cm.replaceRange('~~', c); cm.setCursor(c.line, c.ch + 1); }
}

function addSuperscript(editor) {
  var cm = editor.codemirror;
  var s = cm.getSelection();
  if (s) { cm.replaceSelection('^' + s + '^'); }
  else { var c = cm.getCursor(); cm.replaceRange('^^', c); cm.setCursor(c.line, c.ch + 1); }
}

function addRecentEdits(editor) { editor.codemirror.replaceRange('[wd-recentedits]\n', editor.codemirror.getCursor()); }
function addTotal(editor) { editor.codemirror.replaceRange('[wd-total]\n', editor.codemirror.getCursor()); }

function createTablePicker(editor) {
  var p = document.createElement('div');
  p.className = 'table-picker';
  p.style.cssText = 'display:none;position:absolute;padding:8px;z-index:1000;background:var(--surface-card);border:1px solid var(--border-color);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,0.12);';
  for (var i = 0; i < 10; i++) {
    for (var j = 0; j < 10; j++) {
      var cell = document.createElement('div');
      cell.className = 'cell';
      cell.style.cssText = 'width:20px;height:20px;display:inline-block;margin:2px;cursor:pointer;border:1px solid var(--border-color);border-radius:2px;';
      cell.dataset.row = i + 1;
      cell.dataset.col = j + 1;
      cell.addEventListener('mouseover', function () { highlightCells(this); });
      cell.addEventListener('click', function () { insertTable(editor, parseInt(this.dataset.row), parseInt(this.dataset.col)); });
      p.appendChild(cell);
    }
    p.appendChild(document.createElement('br'));
  }
  document.body.appendChild(p);
  return p;
}

function highlightCells(cell) {
  var row = parseInt(cell.dataset.row), col = parseInt(cell.dataset.col);
  document.querySelectorAll('.table-picker .cell').forEach(function (c) {
    c.style.background = (parseInt(c.dataset.row) <= row && parseInt(c.dataset.col) <= col) ? 'var(--color-primary)' : '';
  });
}

function insertTable(editor, rows, cols) {
  var cm = editor.codemirror, table = '\n';
  table += '| ' + Array(cols).fill('Header').join(' | ') + ' |\n';
  table += '| ' + Array(cols).fill('---').join(' | ') + ' |\n';
  for (var i = 0; i < rows; i++) { table += '| ' + Array(cols).fill('Cell').join(' | ') + ' |\n'; }
  cm.replaceSelection(table);
  var p = document.querySelector('.table-picker');
  if (p) p.style.display = 'none';
}

var simplemde = new EasyMDE({
  element: document.getElementById('simplemde'),
  autoDownloadFontAwesome: false,
  spellChecker: false,
  autofocus: true,
  forceSync: true,
  showIcons: ['code', 'table'],
  blockStyles: { bold: '**', italic: '*', code: '```' },
  toolbar: [
    'bold', 'italic', 'strikethrough', 'heading', '|',
    'code', 'quote', 'unordered-list', 'ordered-list', '|',
    'link', 'image',
    { name: 'insert-table', action: function (ed) {
      var p = document.querySelector('.table-picker') || createTablePicker(ed);
      var btn = ed.toolbarElements['insert-table'];
      var r = btn.getBoundingClientRect();
      p.style.top = (r.bottom + window.scrollY) + 'px';
      p.style.left = (r.left + window.scrollX) + 'px';
      p.style.display = p.style.display === 'none' ? 'block' : 'none';
    }, className: 'fa fa-th', title: 'Insert Custom Table' },
    'horizontal-rule', '|',
    { name: 'subscript', action: addSubscript, className: 'fa fa-subscript', title: 'Add Subscript' },
    { name: 'superscript', action: addSuperscript, className: 'fa fa-superscript', title: 'Add Superscript' },
    { name: 'insert-toc', action: insertTOC, className: 'fa fa-list-alt', title: 'Insert Table of Contents' },
    { name: 'recent-edits', action: addRecentEdits, className: 'fa fa-clock-o', title: 'Insert Recent Edits' },
    { name: 'total', action: addTotal, className: 'fa fa-book', title: 'Insert Total Number of Documents' },
    '|', 'preview', 'side-by-side', 'fullscreen', '|', 'undo', 'redo'
  ]
});

document.addEventListener('click', function (e) {
  var p = document.querySelector('.table-picker');
  if (p && !p.contains(e.target) && !e.target.classList.contains('fa-th')) { p.style.display = 'none'; }
});

var changed = false, changed_draft = false;

simplemde.codemirror.on('change', function () { changed = true; changed_draft = true; });

window.addEventListener('beforeunload', function (e) {
  if (changed) { e.preventDefault(); e.returnValue = 'Unsaved changes!'; return e.returnValue; }
});

document.getElementById('editor-save').addEventListener('click', function () {
  changed = false;
  document.getElementById('editor-form').submit();
});

document.getElementById('editor-revision').addEventListener('click', function () {
  var inp = document.querySelector('input[name="revision"]');
  var svg = document.getElementById('editor-revision-icon-svg');
  if (inp.value === '1') {
    inp.value = '0';
    if (svg) svg.innerHTML = '<use href="#icon-square"/>';
  } else {
    inp.value = '1';
    if (svg) svg.innerHTML = '<use href="#icon-check-square"/>';
  }
});

setInterval(function () {
  if (changed_draft) {
    $.ajax({
      url: APP.URL + 'submit.php?act=draft_save_ajax',
      type: 'POST',
      data: { token: APP.token, document: DOC.ID, content: document.querySelector('textarea[name="content"]').value },
      cache: false,
      success: function (r) {
        var d = JSON.parse(r);
        if (d.error === 1) { showToast(d.code, 'danger'); }
        else { changed_draft = false; }
      },
      error: function (x, s, e) { showToast('Draft error: ' + e, 'danger'); }
    });
  }
}, 10000);