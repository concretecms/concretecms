<div class="preview-frame-container">
    <iframe
        style="display:none"
        src="<?= URL::to('/ccm/system/panels/page/preview_as_user/render') . '?&cID=' . Request::request('cID') ?>"
        data-src="<?= URL::to('/ccm/system/panels/page/preview_as_user/render') ?>">></iframe>
    <div class="cover"></div>
    <div class="loader">
        <div class="icon">
            <i class="fa fa-cog fa-spin"></i>
        </div>
    </div>
</div>
<script type="application/javascript">
    (function (window, $, _) {
        'use strict';

        var container = $('div.preview-frame-container'),
            frame = container.children('iframe'),
            loader = container.children('div.loader'),
            form;

        Concrete.event.bind('PanelOpenDetail', function(e) {
            Concrete.event.unsubscribe(e);

            var bind = _.once(function() {
                form = $('form.preview-panel-form');
                form.change(function() {
                    handleChange();
                });
                form.find('button').click(function() {
                    handleChange();
                });
                form.find('input').keydown(_.debounce(function() {
                    handleChange();
                }, 1000));
            });

            frame.load(function() {
                _.defer(function() {
                    var frame_elem = frame.get(0),
                        frame_window = frame_elem.contentWindow;
                    frame.height($(frame_window.document).height()).width('100%');
                });
                frame.fadeIn();
                loader.fadeOut();
                bind();
            });

        });

        function handleChange() {
            var guest_button = form.find('button.guest-button'),
                user_input = form.find('input.custom-user'),
                query = {
                    cID: CCM_CID,
                    customUser: guest_button.hasClass('active') ? 0 : user_input.val()
                };

            form.find('input[name],select[name]').each(function() {
                var $me = $(this), name = $me.attr('name');
                if(name && (name.indexOf('preview_as_user_datetime_') === 0)) {
                    query[name] = $me.val();
                }
            });

            var src = frame.data('src') + "?" + $.param(query);

            loader.fadeIn(250, function() {
                frame.attr('src', src);
            });
        }

    }(window, jQuery, _));
</script>
