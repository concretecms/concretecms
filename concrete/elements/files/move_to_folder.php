<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
use Concrete\Core\Tree\Node\Node;

$folders = Node::getNodesOfType('file_folder');
?>

<div class="form-group" id="ccm-folder-search">
    <form class="form-inline">
        <input type="search" class="form-control input-sm" data-field="folder-search" autocomplete="off" placeholder="<?=t('Filter Folders')?>" />
    </form>
</div>


<div class="form-group" id="ccm-folder-list">
    <?php if (count($folders)) {
    ?>
        <?php $i = 0;
        foreach ($folders as $index => $folder) {
            
    if ($displayFolder($folder)) {
        $checked = $i === 0 ? 'checked' : false;
        $i++;
        ?>
            <div class="radio li">
                <label>
                <?php echo $getRadioButton($folder, $checked);
        ?>
                <span data-label="folder-name"><?=$folder->getTreeNodeDisplayName()?></span>
                </label>
            </div>
            <?php 
    }
    ?>
        <?php 
}
    ?>
    <?php 
} ?>
</div>

<script type="text/javascript">
    $(function() {
        $('input[data-field=folder-search]').liveUpdate('ccm-folder-list', 'folder').closest('form').unbind('submit.liveupdate');
        ConcreteEvent.subscribe('FolderUpdateRequestComplete', function() {
            window.location.reload();
        });
    });
</script>