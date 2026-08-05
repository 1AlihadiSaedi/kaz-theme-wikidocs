/**
 * WikiDocs — Attachments Script
 * Updated for modern UI (no Materialize dependency)
 */

/* Form upload */
var attachmentsForm = document.getElementById('attachments-uploader-form');
if (attachmentsForm) {
  attachmentsForm.addEventListener('submit', function (e) {
    e.preventDefault();
    var submitBtn = document.getElementById('attachment-upload-btn');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Uploading…';
    }

    $.ajax({
      url: this.getAttribute('action'),
      type: 'POST',
      data: new FormData(this),
      contentType: false,
      cache: false,
      processData: false,
      success: function (response) {
        var decoded = JSON.parse(response);
        if (decoded.error === 1) {
          showToast(decoded.code, 'danger');
        } else {
          var list = document.getElementById('attachments-list');
          var li = document.createElement('li');
          li.innerHTML =
            '<span>📄 ' + decoded.name + '</span>' +
            '<a href="#" class="delete-link attachment-delete" data-attachment="' + decoded.name + '">🗑️ Delete</a>';
          list.appendChild(li);
          showToast('Attachment uploaded', 'success');
        }
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Upload';
        }
      },
      error: function (xhr, status, error) {
        showToast('Error: ' + error, 'danger');
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Upload';
        }
      }
    });
  });
}

/* Attachment delete (delegated) */
document.addEventListener('click', function (e) {
  var deleteBtn = e.target.closest('.attachment-delete');
  if (!deleteBtn) return;
  e.preventDefault();

  var attachmentName = deleteBtn.getAttribute('data-attachment');
  if (!confirm('Delete this attachment?')) return;

  $.ajax({
    url: APP.PATH + 'submit.php?act=attachment_delete_ajax',
    type: 'POST',
    dataType: 'html',
    data: 'token=' + APP.token + '&document=' + DOC.ID + '&attachment_name=' + attachmentName,
    cache: false,
    processData: false,
    success: function (response) {
      var decoded = JSON.parse(response);
      if (decoded.error === 1) {
        showToast(decoded.code, 'danger');
      } else {
        var li = deleteBtn.closest('li');
        if (li) li.remove();
        showToast('Attachment deleted', 'success');
      }
    },
    error: function (xhr, status, error) {
      showToast('Error: ' + error, 'danger');
    }
  });
});