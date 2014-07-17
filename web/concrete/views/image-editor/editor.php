<?
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\ImageEditor\Component as SystemImageEditorComponent;
use Concrete\Core\ImageEditor\ControlSet as SystemImageEditorControlSet;
use Concrete\Core\ImageEditor\Filter as SystemImageEditorFilter;
use Whoops\Exception\ErrorException;

$editorid = substr(sha1(time()), 0, 5); // Just enough entropy.

$u = new User();
$form = Loader::helper('form');
/** @var FileVersion $fv */
$f = $fv->getFile();
$fp = new Permissions($f);
if (!$fp->canEditFileContents()) {
    die(t("Access Denied."));
}

$req = ResponseAssetGroup::get();
$req->requireAsset('core/imageeditor');

$controlsets = SystemImageEditorControlSet::getList();
$components = SystemImageEditorComponent::getList();
$filters = SystemImageEditorFilter::getList();

?>
    <div class='table ccm-ui'>
        <div class='editorcontainer'>
            <div id='<?= $editorid ?>' class='Editor'></div>
            <div class='bottomBar'></div>
        </div>
        <div class='controls'>
            <div class='controlscontainer'>
                <div class='editorcontrols'>
                    <div class='control-sets'>
                        <?php
                        if (!$controlsets) {
                            echo "&nbsp;";
                        }
                        foreach ($controlsets as $controlset) {
                            $handle = $controlset->getHandle();
                            ?>
                            <link rel='stylesheet' href='<?= $controlset->getCssPath() ?>'>
                            <div class="controlset controlset-<?= $controlset->gethandle() ?>"
                                 data-namespace="<?= $controlset->getHandle() ?>"
                                 data-src="<?= $controlset->getJavascriptPath() ?>">
                                <h4><?= $controlset->getDisplayName() ?></h4>

                                <div class="control">
                                    <div class="contents">
                                        <?php
                                        try {
                                            $view = new View;
                                            $view->setInnerContentFile($controlset->getViewPath());
                                            echo $view->renderViewContents(array('control-set' => $controlset));
                                        } catch (ErrorException $e) {
                                            echo t("No view found.");
                                            // File doesn't exist, just continue.
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class='border'></div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class='save'>
                    <button class='cancel btn'>Cancel</button>
                    <button class='save btn pull-right btn-primary'>Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            _.defer(function () {
                var settings = {
                    src: '<?=$fv->getURL()?>',
                    fID: <?= $fv->getFileID() ?>,
                    controlsets: {},
                    filters: {},
                    components: {},
                    debug: false
                };
                $('div.controlset', 'div.controls').each(function () {
                    settings.controlsets[$(this).attr('data-namespace')] = {
                        src: $(this).attr('data-src'),
                        element: $(this).children('div.control').children('div.contents')
                    }
                });
                $('div.component', 'div.controls').each(function () {
                    settings.components[$(this).attr('data-namespace')] = {
                        src: $(this).attr('>getdata-src'),
                        element: $(this).children('div.control').children('div.contents')
                    }
                });
                settings.filters = <?php
      $fnames = array();
      foreach ($filters as $filter) {
        $handle = $filter->getHandle();
        $fnames[$handle] = array(
            "src" => $filter->getJavascriptPath(),
            "name" => $filter->getDisplayName('text'),
            "selector" => '.filter.filter-' . $handle);
      }
      echo Loader::helper('json')->encode($fnames);
    ?>;
                var editor = $('div#<?=$editorid?>.Editor');
                window.im = editor.closest('.ui-dialog-content').css('padding', 0).end().ImageEditor(settings);
            });
        });
    </script>
<?php
foreach ($filters as $filter) {
    ?>
    <link rel='stylesheet' href='<?= $filter->getCssPath() ?>'>
<?php
}
