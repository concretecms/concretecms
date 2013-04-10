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
            $f.find('.ccm-composer-save-status').html('<div class="alert alert-info"><?=t("Page saved at ")?>' + r.time + '</div>');
            if (r.saveurl) {
              $f.data('saveURL', r.saveurl);
            }
            if (r.discardurl) {
              $f.data('discardURL', r.discardurl);
            }
            if (r.publishurl) {
              $f.data('publishURL', r.publishurl);
            }
            if (onComplete) {
              onComplete();
            }
          }
      });
    }

  },

    init: function(options) {

      var settings = $.extend({
        autoSaveEnabled: true,
        autoSaveTimeout: 5000,
        publishReturnMethod: 'reload',
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
        $this.data('discardURL', settings.discardURL);
        $this.data('publishURL', settings.publishURL);

        if (settings.autoSaveEnabled) {
          methods.saveinternal = setInterval(function() {
            methods.private.saveDraft($this);
          }, settings.autoSaveTimeout);
        }

        $this.find('button[data-composer-btn=exit]').on('click', function() {
          settings.onExit();
        });

        $this.find('button[data-composer-btn=discard]').on('click', function() {
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

        $this.find('button[data-composer-btn=save]').on('click', function() {
          clearInterval(methods.saveinterval);
          methods.private.saveDraft($this, function() {
            settings.onAfterSaveAndExit();
          });
        });

        if (settings.publishReturnMethod == 'ajax') {
          $this.on('submit.composer', function() {
            return false;
          });
        }

        $this.find('button[data-composer-btn=publish]').on('click', function() {
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
                    $('#ccm-composer-error-list').show().html(html);
                  } else {
                    $('#ccm-composer-error-list').hide();
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