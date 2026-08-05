/**
 * WikiDocs — Editor Shortcuts
 * Updated for modern UI
 */

document.addEventListener('keydown', function (event) {
  // Ctrl/Cmd+S — Save in edit mode
  if ((event.ctrlKey || event.metaKey) && event.key === 's') {
    event.preventDefault();
    if (window.location.href.includes('?edit') || window.location.href.includes('&edit')) {
      var saveButton = document.getElementById('editor-save');
      if (saveButton) saveButton.click();
    }
  }

  // Ctrl/Cmd+E — Toggle edit mode
  if ((event.ctrlKey || event.metaKey) && event.key === 'e') {
    event.preventDefault();
    if (!window.location.href.includes('?edit') && !window.location.href.includes('&edit')) {
      var currentUrl = window.location.href.split('#')[0];
      var editUrl = currentUrl.includes('?') ? currentUrl + '&edit' : currentUrl + '?edit';
      window.location.href = editUrl;
    }
  }

  // Escape to close sidebar on mobile
  if (event.key === 'Escape') {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');
    if (sidebar && sidebar.classList.contains('mobile-open')) {
      sidebar.classList.remove('mobile-open');
      if (overlay) overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  }
});