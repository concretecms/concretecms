<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<script>
    $(document).ready(function(){
        var doSortCount = function(){
            $('.ccm-faq-entry').each(function(index) {
               $(this).find('.ccm-faq-entry-sort').val(index);
            });
        };
        doSortCount();
        var cloneTemplate = $('.ccm-faq-entry-template').clone(true);
        cloneTemplate.removeClass('.ccm-faq-entry-template');
        $('.ccm-faq-entry-template').remove();

        $(cloneTemplate).add($('.ccm-faq-entry')).find('.ccm-delete-faq-entry').click(function(){
          var deleteIt = confirm('<?php echo t('Are you sure?') ?>');
          if(deleteIt == true) {
              $(this).closest('.ccm-faq-entry').remove();
              doSortCount();
          }
        });

        $('.ccm-faq-block-container').sortable({
            stop: function( event, ui ) {
                doSortCount();  // recount every time icon divs are resorted.
            }
        });

        $('.ccm-add-faq-entry').click(function(){
            var newClone = cloneTemplate.clone(true);
            newClone.show();
            $('.ccm-faq-block-container').append(newClone);
            doSortCount();
        });
    });
</script>
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

    .ccm-faq-block-container i.fa-arrows {
        position: absolute;
        top: 10px;
        right: 10px;
    }
</style>
<div class="ccm-faq-block-container">
    <span class="btn btn-success ccm-add-faq-entry"><?php echo t('Add Entry') ?></span>
    <?php if($rows) {
    foreach ($rows as $row) { ?>
        <div class="ccm-faq-entry well">
            <i class="fa fa-arrows"></i>
            <div class="form-group">
                <label><?php echo t('FAQ Entry Anchor Link Text') ?></label>
                <input type="text" name="linkTitle[]" value="<?php echo $row['linkTitle'] ?>" />
            </div>
            <div class="form-group">
                <label><?php echo t('FAQ Entry Title Text') ?></label>
                <input type="text" name="title[]" value="<?php echo $row['title'] ?>" />
            </div>
            <div class="form-group">
                <label><?php echo t('FAQ Entry Description') ?></label>
                <textarea name="description[]"><?php echo $row['description'] ?></textarea>
            </div>
                <input class="ccm-faq-entry-sort" type="hidden" name="sortOrder[]" value="<?php echo $row['sortOrder'] ?>"/>
            <div class="form-group">
                <span class="btn btn-danger ccm-delete-faq-entry"><?php echo t('Delete Entry'); ?></span>
            </div>
        </div>
    <?php }
    }?>
    <div class="ccm-faq-entry well ccm-faq-entry-template"style="display: none;">
        <i class="fa-arrows fa"></i>
        <div class="form-group">
            <label><?php echo t('FAQ Entry Anchor Link Text') ?></label>
            <input type="text" name="linkTitle[]" value="" />
        </div>
        <div class="form-group">
            <label><?php echo t('FAQ Entry Title Text') ?></label>
            <input type="text" name="title[]" value="" />
        </div>
        <div class="form-group">
            <label><?php echo t('FAQ Entry Description') ?></label>
            <textarea name="description[]"></textarea>
        </div>
            <input class="ccm-faq-entry-sort" type="hidden" name="sortOrder[]" value=""/>
        <div class="form-group">
            <span class="btn btn-danger ccm-delete-faq-entry"><?php echo t('Delete Entry'); ?></span>
        </div>
    </div>
</div>
