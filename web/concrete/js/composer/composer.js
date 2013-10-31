(function($, window) {

  var methods = {

    saveinterval: false,

    private:  {

      saveDraft: function(task, $f, onComplete) {
        var settings = $f.data('settings');

        formData = $f.serializeArray();
        formData.push({
          'name': 'task',
          'value': task
        }, {
          name: 'token',
          value: settings.token
        }, {
          name: 'cID',
          value: settings.cID
        });

        $.ajax({
          dataType: 'json',
          type: 'post',
          data: formData,
          url: $f.data('saveURL'),
          error: function(r) {
            methods.private.handleAJAXGeneralError(r);
          },
          success: function(r) {
            if (r.saveURL) {
              $f.data('saveURL', r.saveURL);
            }
            if (r.viewURL) {
              $f.data('viewURL', r.viewURL);
            }
            if (r.discardURL) {
              $f.data('discardURL', r.discardURL);
            }
            if (!methods.private.handleAJAXResponseError(r, $('#ccm-page-type-composer-form-error-list'))) {
              if (r.saveStatus) {
                $('#ccm-page-type-composer-form-save-status').html(r.saveStatus).show();
              }
              if (settings.autoSavePushViewState && r.viewURL != document.URL) {
                if (window.history) {
                  window.history.replaceState({'method': 'loaddraft'}, '', r.viewURL);
                  settings.viewURL = r.viewURL;
                }
              }
              if (onComplete) {
                onComplete(r);
              }
            }
          }
      });
    },

    handleAJAXGeneralError: function(r) {
      ccmAlert.notice('Error', '<div class="alert alert-danger">' + r.responseText + '</div>');
    },

    handleAJAXResponseError: function(r, div) {
      if (r.error == true) {
        if (!div) {
          ccmAlert.notice('Error', '<div class="alert alert-danger">' + r.errors.join("<br>") + '</div>');
        } else {
          div.show().html(r.errors.join("<br>"));
        }
        return true;
      }
    }

  },

    init: function(options) {

      var settings = $.extend({
        cID: false,
        token: false,
        discardURL: CCM_TOOLS_PATH + '/pages/draft/discard',
        saveURL: CCM_TOOLS_PATH + '/pages/composer/save',
        onExit: function() {
          window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/composer/drafts';
        },
        onAfterDiscard: function(r) {
          window.location.href = r.redirectURL;
        },
        autoSaveEnabled: true,
        autoSavePushViewState: false,
        autoSaveTimeout: 5000,
        onAfterSaveAndExit: function(r) {
          window.location.href = r.redirectURL;
        },
        onAfterSaveAndPreview: function(r) {
          window.location.href = r.redirectURL;
        },
        onAfterPublish: function(r) {
          window.location.href = r.redirectURL;
        }
      }, options);

      return this.each(function() {

        var $this = $(this);
        $this.data('settings', settings);
        $this.data('saveURL', settings.saveURL);
        $this.data('viewURL', settings.viewURL);
        $this.data('discardURL', settings.discardURL);

        if (settings.autoSaveEnabled) {
          methods.saveinterval = setInterval(function() {
            methods.private.saveDraft('autosave', $this, function(r) {});
          }, settings.autoSaveTimeout);
        }

        $('button[data-page-type-composer-form-btn=preview]').unbind().on('click', function() {
          clearInterval(methods.saveinterval);
          methods.private.saveDraft('preview', $this, function(r) {
            settings.onAfterSaveAndPreview(r);
          });
        });

        $('button[data-page-type-composer-form-btn=exit]').unbind().on('click', function() {
          settings.onExit();
        });

        $('button[data-page-type-composer-form-btn=discard]').unbind().on('click', function() {
          $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'token': settings.token, 'cID': settings.cID},
            url: $this.data('discardURL'),
            success: function(r) {
              if (!methods.private.handleAJAXResponseError(r)) {
                settings.onAfterDiscard(r);
              }
            },
            error: function(r) {
              methods.private.handleAJAXGeneralError(r);
            },
          });
        });

        $('button[data-page-type-composer-form-btn=save]').unbind().on('click', function() {
          clearInterval(methods.saveinterval);
          methods.private.saveDraft('save', $this, function(r) {
            settings.onAfterSaveAndExit(r);
          });
        });

        $this.on('submit.composer', function() {
          return false;
        });

        $('button[data-page-type-composer-form-btn=publish]').unbind().on('click', function() {
          clearInterval(methods.saveinterval);
          methods.private.saveDraft('publish', $this, function(r) {
            settings.onAfterPublish(r);
          });
        });

       });

    },

    disableAutoSave: function() {
      clearInterval(methods.saveinterval);
    }


  };

  $.fn.ccmcomposer = function(method) {

    if ( methods[method] ) {
      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.ccmcomposer' );
    }   

  };
})(jQuery, window);