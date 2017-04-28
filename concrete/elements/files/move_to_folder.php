<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\File\Filesystem;
$filesystem = new Filesystem();
$rootfolder = $filesystem->getRootFolder();
$folders = $rootfolder->getHierarchicalNodesOfType('file_folder', 1, true, true);
?>

<div class="form-group" id="ccm-folder-search">
    <form class="form-inline">
        <input type="search" class="form-control input-sm" data-field="folder-search" autocomplete="off" placeholder="<?=t('Filter Folders')?>" />
    </form>
</div>

<div class="form-group" id="ccm-folder-list">
<?php
    if (count($folders)) {
        $added_spaces = 0;
        $incrementor = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $previous_level = 0;
        foreach ($folders as $folder) {
            echo '<div class="radio li">'; //opens a folder item
            $folderObject = $folder['treeNodeObject'];
                    
            if ($isCurrentFolder($folderObject)) {
                $checked = 'checked';
            } else {
                $checked = false;
            }
                        
            if ($previous_level > $folder['level']) {
                $added_spaces -= ($previous_level - $folder['level']);
            }
?>
            <label>
                <?php echo str_repeat($incrementor, $added_spaces); ?>
                <?php echo $getRadioButton($folderObject, $checked); ?>
                <span data-label="folder-name"><?=$folderObject->getTreeNodeDisplayName()?></span>
            </label>
<?php
            if ($folder['total'] > 0) {
                $added_spaces++;
            } else {
                if ($folder['level'] < $previous_level) {
                    $added_spaces = 0;
                }
            }
            echo '</div>'; //closes a folder item
            $previous_level = $folder['level'];
        }
    }
?>
</div>

<script type="text/javascript">
    $(function() {
        $('input[data-field=folder-search]').liveUpdate('ccm-folder-list', 'folder').closest('form').unbind('submit.liveupdate');
    });
</script>