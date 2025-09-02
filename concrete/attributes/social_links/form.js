var ConcreteSocialLinksAttribute = {
    init: function (akID) {
        var $root = $('[data-attribute-key-id="' + akID + '"]');
        if (!$root.length) return;

        // Add a new row (scoped to this akID)
        $root.on('click', '.ccm-social-link-attribute-add-service', function (e) {
            e.preventDefault();

            var $wrapper = $root.find('.ccm-social-link-attribute-wrapper').first();
            var $template = $wrapper.find('.ccm-social-link-attribute').first();
            var $clone = $template.clone();

            $clone.find('input').val('');
            $clone.appendTo($wrapper);
            $clone.find('select').trigger('change');

            var $buttons = $wrapper.find('button.ccm-social-link-attribute-remove-line');
            $buttons.show().first().hide();
        });

        // Remove a row (scoped)
        $root.on('click', 'button.ccm-social-link-attribute-remove-line', function (e) {
            e.preventDefault();

            var $wrapper = $(this).closest('.ccm-social-link-attribute-wrapper');
            $(this).closest('.ccm-social-link-attribute').remove();

            var $buttons = $wrapper.find('button.ccm-social-link-attribute-remove-line');
            $buttons.show().first().hide();
        });

        // Change handler (scoped)
        $root.on('change', 'select', function () {
            var $select = $(this);
            var $opt = $select.find(':selected');
            var $row = $select.closest('.ccm-social-link-attribute');

            var icon = $opt.attr('data-icon') || '';
            var faClass = (icon === 'phone-square' || icon === 'envelope' || icon === 'external-link-alt') ? 'fas' : 'fab';

            $row.find('.ccm-social-link-service-text-wrapper').addClass('input-prepend');
            $row.find('.ccm-social-link-service-add-on-wrapper').show();
            $row.find('.add-on i').attr('class', (faClass + ' fa-' + icon).trim());

            var $wrapper = $select.closest('.ccm-social-link-attribute-wrapper');
            var $buttons = $wrapper.find('button.ccm-social-link-attribute-remove-line');
            $buttons.show().first().hide();
        });

        // Initial state for this akID only
        $root.find('.ccm-social-link-attribute-wrapper select').trigger('change');
        $root.find('.ccm-social-link-attribute-wrapper').each(function () {
            var $buttons = $(this).find('button.ccm-social-link-attribute-remove-line');
            $buttons.show().first().hide();
        });
    }
};
