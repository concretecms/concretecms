<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="position-relative">

    <button class="btn btn-sm btn-secondary position-absolute" style="top: 0; right: 0; display: none" id="ccm-dashboard-environment-info-copy"><?=t('Copy')?></button>

    <div id="ccm-dashboard-environment-info"><i class="fa fa-spin fa-sync text-muted"></i> <?=t('Loading environment information...')?></div>

</div>

<script type="text/javascript">
$(document).ready(function() {
    $.get('<?= $view->action('get_environment_info'); ?>').then(function(data) {
        $('#ccm-dashboard-environment-info-copy').show()
        $('#ccm-dashboard-environment-info').html('<pre><code>' + data + '</code></pre>');
        $('#ccm-dashboard-environment-info-copy').click(function() {
            var textArea = document.createElement('textarea')
            textArea.value = data
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            document.execCommand('copy')
            textArea.remove()
            $('#ccm-dashboard-environment-info-copy').prop('disabled', true).text('<?=t('Copied!')?>')
        })
    });
});
</script>
