window.ConcreteSocialLinksAttribute = window.ConcreteSocialLinksAttribute || {
    init(akID) {
        const $root = $('[data-attribute-key-id="' + akID + '"]');
        if (!$root.length) {
            return;
        }
        // Add a new row (scoped to this akID)
        $root.on('click', '.ccm-social-link-attribute-add-service', function (e) {
            e.preventDefault();
            const $wrapper = $root.find('.ccm-social-link-attribute-wrapper').first();
            const $template = $wrapper.find('.ccm-social-link-attribute').first();
            const $clone = $template.clone();
            $clone.find('input').val('');
            $clone.appendTo($wrapper);
            $clone.find('select').trigger('change');
            const $buttons = $wrapper.find('button.ccm-social-link-attribute-remove-line');
            $buttons.show().first().hide();
        });

        // Remove a row (scoped)
        $root.on('click', 'button.ccm-social-link-attribute-remove-line', function (e) {
            e.preventDefault();
            const $wrapper = $(this).closest('.ccm-social-link-attribute-wrapper');
            $(this).closest('.ccm-social-link-attribute').remove();
            const $buttons = $wrapper.find('button.ccm-social-link-attribute-remove-line');
            $buttons.show().first().hide();
        });

        // Change handler (scoped)
        $root.on('change', 'select', function () {
            const $select = $(this);
            const $opt = $select.find(':selected');
            const $row = $select.closest('.ccm-social-link-attribute');
            const icon = $opt.attr('data-icon-html');
            if (icon) {
                $row.find('.ccm-social-link-attribute-icon').html(icon);
                $row.find('.ccm-social-link-attribute-icon').show();
            } else {
                $row.find('.ccm-social-link-attribute-icon').hide();
            }
            const $wrapper = $select.closest('.ccm-social-link-attribute-wrapper');
            const $buttons = $wrapper.find('button.ccm-social-link-attribute-remove-line');
            $buttons.show().first().hide();
        });

        // Initial state for this akID only
        $root.find('.ccm-social-link-attribute-wrapper select').trigger('change');
        $root.find('.ccm-social-link-attribute-wrapper').each(function () {
            const $buttons = $(this).find('button.ccm-social-link-attribute-remove-line');
            $buttons.show().first().hide();
        });
    }
};
