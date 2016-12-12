<?php defined('C5_EXECUTE') or die("Access Denied.");

$fp = FilePermissions::getGlobal();
$tp = new TaskPermission();
?>

<style>
    .ccm-faq-block-container input,
    .ccm-faq-block-container textarea {
        display: block;
        width: 100%;
    }

    .ccm-faq-block-container .btn-success {
        margin-bottom: 20px;
    }

    .ccm-faq-entry {
        position: relative;
    }

    .ccm-faq-block-container i.fa-sort-asc {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
    }

    .ccm-faq-block-container i:hover {
        color: #5cb85c;
    }

    .ccm-faq-block-container i.fa-sort-desc {
        position: absolute;
        top: 15px;
        cursor: pointer;
        right: 10px;
    }
</style>
<div class="ccm-faq-block-container">
    <span class="btn btn-success ccm-add-faq-entry"><?php echo t('Add Entry') ?></span>
    <?php if ($rows) {
    foreach ($rows as $row) { ?>
        <div class="ccm-faq-entry well">
            <i class="fa-sort-asc fa"></i>
            <i class="fa-sort-desc fa"></i>

            <div class="form-group">
                <label><?php echo t('Navigation Link Text') ?></label>
                <input type="text" name="linkTitle[]" value="<?php echo $row['linkTitle'] ?>"/>
            </div>
            <div class="form-group">
                <label><?php echo t('Title Text') ?></label>
                <input type="text" name="title[]" value="<?php echo $row['title'] ?>"/>
            </div>
            <div class="form-group">
                <label><?php echo t('Description') ?></label>
                <textarea class='redactor-content' name="description[]"><?php echo $row['description'] ?></textarea>
            </div>
            <input class="ccm-faq-entry-sort" type="hidden" name="sortOrder[]" value="<?php echo $row['sortOrder'] ?>"/>

            <div class="form-group">
                <span class="btn btn-danger ccm-delete-faq-entry"><?php echo t('Delete Entry'); ?></span>
            </div>
        </div>
    <?php }
    } else { ?>
        <script>
            _.defer(function () {
                $('.ccm-add-faq-entry').click();
            });
        </script>
    <?php } ?>
    <div class="ccm-faq-entry well ccm-faq-entry-template" style="display: none;">
        <i class="fa-sort-asc fa"></i>
        <i class="fa-sort-desc fa"></i>

        <div class="form-group">
            <label><?php echo t('Navigation Link Text') ?></label>
            <input type="text" name="linkTitle[]" value=""/>
        </div>
        <div class="form-group">
            <label><?php echo t('Title Text') ?></label>
            <input type="text" name="title[]" value=""/>
        </div>
        <div class="form-group">
            <label><?php echo t('Description') ?></label>
            <textarea class='redactor-content' name="description[]"></textarea>
        </div>
        <input class="ccm-faq-entry-sort" type="hidden" name="sortOrder[]" value=""/>

        <div class="form-group">
            <span class="btn btn-danger ccm-delete-faq-entry"><?php echo t('Delete Entry'); ?></span>
        </div>
    </div>
</div>

<script>
    (function() {
        var container = $('.ccm-faq-block-container');
        var doSortCount = function () {
            $('.ccm-faq-entry', container).each(function (index) {
                $(this).find('.ccm-faq-entry-sort').val(index);
            });
        };
        doSortCount();
        var cloneTemplate = $('.ccm-faq-entry-template', container).clone(true);
        cloneTemplate.removeClass('.ccm-faq-entry-template');
        $('.ccm-faq-entry-template').remove();

        $(cloneTemplate).add($('.ccm-faq-entry', container)).find('.ccm-delete-faq-entry').click(function () {
            var deleteIt = confirm('<?php echo t('Are you sure?') ?>');
            if (deleteIt == true) {
                $(this).closest('.ccm-faq-entry').remove();
                doSortCount();
            }
        });

        container.find('.redactor-content').redactor({
            minHeight: 200,
            'concrete5': {
                filemanager: <?=$fp->canAccessFileManager()?>,
                sitemap: <?=$tp->canAccessSitemap()?>,
                lightbox: true
            }
        });

        var attachSortDesc = function ($obj) {
            $obj.click(function () {
                var myContainer = $(this).closest('.ccm-faq-entry');
                myContainer.insertAfter(myContainer.next('.ccm-faq-entry'));
                doSortCount();
            });
        };

        var attachSortAsc = function ($obj) {
            $obj.click(function () {
                var myContainer = $(this).closest('.ccm-faq-entry');
                myContainer.insertBefore(myContainer.prev('.ccm-faq-entry'));
                doSortCount();
            });
        };
        $('i.fa-sort-desc', container).each(function () {
            attachSortDesc($(this));
        });
        $('i.fa-sort-asc', container).each(function () {
            attachSortAsc($(this));
        });
        $('.ccm-add-faq-entry', container).click(function () {
            var newClone = cloneTemplate.clone(true);

            newClone.show().find('.redactor-content').redactor({
                minHeight: 200,
                'concrete5': {
                    filemanager: <?=$fp->canAccessFileManager()?>,
                    sitemap: <?=$tp->canAccessSitemap()?>,
                    lightbox: true
                }
            });
            container.append(newClone);
            attachSortAsc(newClone.find('i.fa-sort-asc'));
            attachSortDesc(newClone.find('i.fa-sort-desc'));
            var thisModal = $(this).closest('.ui-dialog-content');
            var newSlide = $('.ccm-faq-entry').last();
            thisModal.scrollTop(newSlide.offset().top);
            doSortCount();
        });
    }());
</script>
