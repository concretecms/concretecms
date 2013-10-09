(function($, window) {

  var methods = {

    saveinterval: false,

    private:  {

      saveDraft: function($f, onComplete) {        
        formData = $f.serializeArray();
        $.ajax({
          dataType: 'json',
          type: 'post',
          data: formData,
          url: $f.data('saveURL'),
          success: function(r) {
            $f.find('.ccm-page-type-composer-form-save-status').html('<div class="alert alert-info">' + r.saveStatus + '</div>');
            if (r.saveURL) {
              $f.data('saveURL', r.saveURL);
            }
            if (r.viewURL) {
              $f.data('viewURL', r.viewURL);
            }
            if (r.discardURL) {
              $f.data('discardURL', r.discardURL);
            }
            if (r.publishURL) {
              $f.data('publishURL', r.publishURL);
            }
            var settings = $f.data('settings');
            if (settings.pushStateOnSave && r.viewURL != settings.viewURL) {
              if (window.history) {
                window.history.replaceState({'method': 'loaddraft'}, '', r.viewURL);
                settings.viewURL = r.viewURL;
              }
            }
            if (onComplete) {
              onComplete(r);
            }
          }
      });
    }

  },

    init: function(options) {

      var settings = $.extend({
        autoSaveEnabled: true,
        autoSaveTimeout: 5000,
        pushStateOnSave: false,
        publishReturnMethod: 'reload',
        onPublish: false,
        onExit: function() {
          window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/composer/drafts';
        },
        onAfterDiscard: function() {
          window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/composer/drafts';
        },
        onAfterSaveAndExit: function() {
          window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/composer/drafts';
        }        
      }, options);

      return this.each(function() {

        var $this = $(this);
        $this.data('settings', settings);
        $this.data('saveURL', settings.saveURL);
        $this.data('viewURL', settings.viewURL);
        $this.data('discardURL', settings.discardURL);
        $this.data('publishURL', settings.publishURL);

        if (settings.autoSaveEnabled) {
          methods.saveinternal = setInterval(function() {
            methods.private.saveDraft($this, function(r) {
              if (parseInt(settings.cID) == 0) {
                // this is the first auto-save.
                $this.find('button[data-page-type-composer-form-btn=permissions]').show();
                settings.cID = r.cID;
              }
            });
          }, settings.autoSaveTimeout);
        }

        $this.find('button[data-page-type-composer-form-btn=exit]').on('click', function() {
          settings.onExit();
        });

        if (parseInt(settings.cID) > 0) {
          $this.find('button[data-page-type-composer-form-btn=permissions]').show();
        }

        /*
        $this.find('button[data-page-type-composer-form-btn=permissions]').on('click', function() {
          jQuery.fn.dialog.open({
            href: CCM_TOOLS_PATH + '/composer/draft/permissions?cID=' + settings.cID,
            width: 400,
            height: 290,
            title: ccmi18n.pDraftPermissionsTitle
          });
        });
*/

        $this.find('button[data-page-type-composer-form-btn=discard]').on('click', function() {
          if (!$this.data('discardURL')) {
            settings.onAfterDiscard();
          } else {
            $.ajax({
              type: 'get',
              url: $this.data('discardURL'),
              success: function(r) {
                settings.onAfterDiscard();
              }
            });
          }
        });

        $this.find('button[data-page-type-composer-form-btn=save]').on('click', function() {
          clearInterval(methods.saveinterval);
          methods.private.saveDraft($this, function(r) {
            settings.onAfterSaveAndExit();
          });
        });

        if (settings.publishReturnMethod == 'ajax') {
          $this.on('submit.composer', function() {
            return false;
          });
        }

        $this.find('button[data-page-type-composer-form-btn=publish]').on('click', function() {
          clearInterval(methods.saveinterval);
          $this.attr('action', $this.data('publishURL'));
          if (settings.publishReturnMethod == 'ajax') {
            $this.on('submit.composer', function() {
              $(this).prop('disabled', true);
              formData = $this.serializeArray();
              $.ajax({
                type: 'post',
                data: formData,
                dataType: 'json',
                url: $this.data('publishURL'),
                success: function(r) {
                  $(this).prop('disabled', false);
                  if (r.error) {
                    var html = '';
                    for (i = 0; i < r.messages.length; i++) {
                      html += '<div>' + r.messages[i] + '</div>';
                    }
                    $('#ccm-page-type-composer-form-error-list').show().html(html);
                  } else {
                    $('#ccm-page-type-composer-form-error-list').hide();
                    if (settings.onPublish) {
                      settings.onPublish(r);
                    }
                  }
                },
                complete: function() {
                  $this.unbind('submit.composer').on('submit.composer', function() {
                    return false;
                  });
                  $this.removeAttr('action');
                }
              });
              return false;
            });
          }
          $this.submit();
        });

      });

    },


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