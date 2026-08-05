/**
 * WikiDocs — Images Script
 * Updated for modern UI (no Materialize dependency)
 */

/* Image picker — insert into editor */
document.addEventListener('click', function (e) {
  var picker = e.target.closest('.image-picker');
  if (picker) {
    e.preventDefault();
    var imageName = picker.getAttribute('data-image');
    simplemde.codemirror.replaceSelection('![](' + DOC.PATH + '/' + imageName + ')');
    if (window.WD && window.WD.closeModal) {
      window.WD.closeModal('modal-images');
    }
  }
});

/* Image delete */
document.addEventListener('click', function (e) {
  var deleteBtn = e.target.closest('.image-delete');
  if (!deleteBtn) return;
  e.preventDefault();

  var imageName = deleteBtn.getAttribute('data-image');
  if (!confirm(confirm_image_delete)) return;

  $.ajax({
    url: APP.PATH + 'submit.php?act=image_delete_ajax',
    type: 'POST',
    dataType: 'html',
    data: 'token=' + APP.token + '&document=' + DOC.ID + '&image_name=' + imageName,
    cache: false,
    processData: false,
    success: function (response) {
      var decoded = JSON.parse(response);
      if (decoded.error === 1) {
        showToast(decoded.code, 'danger');
      } else {
        var item = deleteBtn.closest('.image-grid-item');
        if (item) item.remove();
        showToast('Image deleted', 'success');
      }
    },
    error: function (xhr, status, error) {
      showToast('Error: ' + error, 'danger');
    }
  });
});

/* Form upload */
var imagesForm = document.getElementById('images-uploader-form');
if (imagesForm) {
  imagesForm.addEventListener('submit', function (e) {
    e.preventDefault();
    var submitBtn = document.getElementById('image-upload-btn');
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
          var grid = document.getElementById('images-list');
          var item = document.createElement('div');
          item.className = 'image-grid-item';
          item.innerHTML =
            '<a href="#" class="image-picker" data-image="' + decoded.name + '">' +
            '<img class="polaroid" src="' + decoded.path + '" alt="' + decoded.name + '" loading="lazy">' +
            '</a>' +
            '<a href="#" class="delete-link image-delete" data-image="' + decoded.name + '">🗑️ Delete</a>';
          grid.appendChild(item);
          showToast('Image uploaded', 'success');
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

/* Paste image from clipboard */
window.addEventListener('paste', function (e) {
  retrieveImageFromClipboardAsBase64(e, function (imageDataBase64) {
    if (!imageDataBase64) return;
    $.ajax({
      url: APP.PATH + 'submit.php?act=image_upload_ajax',
      type: 'POST',
      dataType: 'html',
      data: 'token=' + APP.token + '&document=' + DOC.ID + '&image_base64=' + imageDataBase64,
      cache: false,
      processData: false,
      success: function (response) {
        var decoded = JSON.parse(response);
        if (decoded.error === 1) {
          showToast(decoded.code, 'danger');
        } else {
          var grid = document.getElementById('images-list');
          if (grid) {
            var item = document.createElement('div');
            item.className = 'image-grid-item';
            item.innerHTML =
              '<a href="#" class="image-picker" data-image="' + decoded.name + '">' +
              '<img class="polaroid" src="' + decoded.path + '" alt="' + decoded.name + '" loading="lazy">' +
              '</a>' +
              '<a href="#" class="delete-link image-delete" data-image="' + decoded.name + '">🗑️ Delete</a>';
            grid.appendChild(item);
          }
          simplemde.codemirror.replaceSelection('![](' + decoded.path + ')');
          showToast('Image pasted', 'success');
        }
      },
      error: function (xhr, status, error) {
        showToast('Error: ' + error, 'danger');
      }
    });
  });
}, false);

/* Drag & drop image upload */
document.addEventListener('dragover', function (e) {
  e.preventDefault();
});

document.addEventListener('drop', function (e) {
  e.preventDefault();
  for (var i = 0; i < e.dataTransfer.files.length; i++) {
    (function (file) {
      getBase64(file).then(function (data) {
        $.ajax({
          url: APP.PATH + 'submit.php?act=image_drop_upload_ajax',
          type: 'POST',
          dataType: 'html',
          data: 'token=' + APP.token + '&document=' + DOC.ID + '&image_base64=' + data + '&image_name=' + file.name,
          cache: false,
          processData: false,
          success: function (response) {
            var decoded = JSON.parse(response);
            if (decoded.error === 1) {
              showToast(decoded.code, 'danger');
            } else {
              var grid = document.getElementById('images-list');
              if (grid) {
                var item = document.createElement('div');
                item.className = 'image-grid-item';
                item.innerHTML =
                  '<a href="#" class="image-picker" data-image="' + decoded.name + '">' +
                  '<img class="polaroid" src="' + decoded.path + '" alt="' + decoded.name + '" loading="lazy">' +
                  '</a>' +
                  '<a href="#" class="delete-link image-delete" data-image="' + decoded.name + '">🗑️ Delete</a>';
                grid.appendChild(item);
              }
              simplemde.codemirror.replaceSelection('![](' + decoded.path + ')\n');
            }
          },
          error: function (xhr, status, error) {
            showToast('Error: ' + error, 'danger');
          }
        });
      });
    })(e.dataTransfer.files[i]);
  }
});

function getBase64(file) {
  return new Promise(function (resolve, reject) {
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function () { resolve(reader.result); };
    reader.onerror = function (error) { reject(error); };
  });
}

function retrieveImageFromClipboardAsBase64(pasteEvent, callback, imageFormat) {
  if (!pasteEvent.clipboardData) { if (typeof callback === 'function') callback(undefined); return; }
  var items = pasteEvent.clipboardData.items;
  if (!items) { if (typeof callback === 'function') callback(undefined); return; }
  for (var i = 0; i < items.length; i++) {
    if (items[i].type.indexOf('image') === -1) continue;
    var blob = items[i].getAsFile();
    var mycanvas = document.createElement('canvas');
    var ctx = mycanvas.getContext('2d');
    var img = new Image();
    img.onload = function () {
      mycanvas.width = this.width;
      mycanvas.height = this.height;
      ctx.drawImage(img, 0, 0);
      if (typeof callback === 'function') callback(mycanvas.toDataURL(imageFormat || 'image/png'));
    };
    var URLObj = window.URL || window.webkitURL;
    img.src = URLObj.createObjectURL(blob);
  }
}