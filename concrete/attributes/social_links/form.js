var ConcreteSocialLinksAttribute = {

    init: function(akID) {
        $('.ccm-attribute-form-wrapper[data-id=' + akID + '] .ccm-social-link-attribute-add-service').click(function() {
            $(this).closest('.ccm-attribute-form-wrapper').find('.ccm-social-link-attribute').first().
                clone().
                appendTo($('.ccm-social-link-attribute-wrapper')).
                find('input').
                val('').
                closest('.ccm-social-link-attribute').
                find('select').
                trigger('change');
            $('button.ccm-social-link-attribute-remove-line').show();
            $('button.ccm-social-link-attribute-remove-line:first').hide();
        });
        $('.ccm-social-link-attribute-wrapper').on('click', 'button.ccm-social-link-attribute-remove-line', function() {
            $(this).closest('.ccm-social-link-attribute').remove();
        });
        $('.ccm-social-link-attribute-wrapper').on('change', 'select', function() {
            var opt = $($(this).find(':selected'));
            var $parentContainer = $(this).closest('.ccm-social-link-attribute');
            $('button.ccm-social-link-attribute-remove-line').show();
            $('button.ccm-social-link-attribute-remove-line:first').hide();

            $parentContainer.
                find('.ccm-social-link-service-text-wrapper').
                addClass('input-prepend').
                find('.ccm-social-link-service-add-on-wrapper').show().
                find('.add-on i').
                removeClass().
                addClass('fa fa-'+opt.attr('data-icon'));

        });
        $('.ccm-social-link-attribute-wrapper select').trigger('change');
    }
};


