<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>  

<div style="border: 1px solid #eee; width: 100%; height: 556px" id="ccm-block-twig-value"><?php echo htmlspecialchars($content ?? '', ENT_QUOTES, APP_CHARSET); ?></div>
<textarea style="display: none" id="ccm-block-twig-value-textarea" name="content"></textarea>

<script type="text/javascript">
    $(function() {
        var editor = ace.edit("ccm-block-twig-value");
        editor.setTheme("ace/theme/eclipse");
        editor.getSession().setMode("ace/mode/twig");
        refreshTextarea(editor.getValue());
        editor.getSession().on('change', function() {
            refreshTextarea(editor.getValue());
        });
    });

    function refreshTextarea(contents) {
        $('#ccm-block-twig-value-textarea').val(contents);
    }
</script>
