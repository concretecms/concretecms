<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-search="users" class="ccm-ui">

    <?php
    $header->render();
    ?>

    <?php Loader::element('users/search', array('result' => $result))?>

</div>

<script type="text/javascript">
    $(function() {
        $('div[data-search=pages]').concreteAjaxSearch({
            result: <?=json_encode($result->getJSONObject())?>
        });
    });
</script>