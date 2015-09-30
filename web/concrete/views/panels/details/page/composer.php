<?
defined('C5_EXECUTE') or die("Access Denied.");
$cID = $c->getCollectionID();
?>

<section class="ccm-ui">
	<header><?=t('Composer - %s', $pagetype->getPageTypeDisplayName())?></header>
	<form method="post" data-panel-detail-form="compose">
		<?=Loader::helper('concrete/ui/help')->display('panel', '/page/composer')?>

		<?php Loader::helper('concrete/composer')->display($pagetype, $c); ?>
	</form>

	<div class="ccm-panel-detail-form-actions dialog-buttons">
		<?php Loader::helper('concrete/composer')->displayButtons($pagetype, $c); ?>
	</div>
</section>

<script type="text/javascript">
ConcretePageComposerDetail = {

	timeout: 5000,
	saving: false,
	interval: false,
	$form: $('form[data-panel-detail-form=compose]'),

	saveDraft: function(onComplete) {
		var my = this;
		my.$form.concreteAjaxForm({
    		'beforeSubmit': function() {
    			my.saving = true;
    		},
			url: '<?=$controller->action('autosave')?>',
			success: function(r) {
				my.saving = false;
		        $('#ccm-page-type-composer-form-save-status').html(r.message).show();
		        if (onComplete) {
		        	onComplete(r);
		        }
			}
		}).submit();
	},

	enableAutosave: function() {
		var my = this;
		my.interval = setInterval(function() {
			ConcretePageComposerDetail.saveDraft();
		}, my.timeout);
	},

	disableAutosave: function() {
		var my = this;
	   	clearInterval(my.interval);
	},

	start: function() {
		var my = this;
	    $('button[data-page-type-composer-form-btn=discard]').on('click', function() {
			if (confirm('<?=t('This will remove this draft and it cannot be undone. Are you sure?')?>')) {
		    	my.disableAutosave();
		    	$.concreteAjax({
		    		'url': '<?=$controller->action('discard')?>',
		    		'data': {cID: '<?=$cID?>'},
		    		success: function(r) {
						window.location.href = r.redirectURL;
		    		}
		    	});
			}
		});

	    $('button[data-page-type-composer-form-btn=preview]').on('click', function() {
	    	my.disableAutosave();
	    	redirect = function () {
	   			window.location.href = CCM_DISPATCHER_FILENAME + '?cID=<?=$cID?>&ctask=check-out&<?=Loader::helper('validation/token')->getParameter()?>';
	    	}
	    	if (!my.saving) {
	    		my.saveDraft(redirect);
	    	} else {
	    		redirect();
	    	}
		});

        $('button[data-page-type-composer-form-btn=exit]').on('click', function() {
            my.disableAutosave();
            var submitSuccess = false;
            my.$form.concreteAjaxForm({
                url: '<?=$controller->action('save_and_exit')?>',
                success: function(r) {
                    submitSuccess = true;
                    window.location.href = r.redirectURL;
                },
                complete: function() {
                    if (!submitSuccess) {
                        my.enableAutosave();
                    }
                    jQuery.fn.dialog.hideLoader();
                }
            }).submit();
        });

        $('button[data-page-type-composer-form-btn=publish]').on('click', function() {
	    	my.disableAutosave();
	    	var submitSuccess = false;
			my.$form.concreteAjaxForm({
				url: '<?=$controller->action('publish')?>',
				success: function(r) {
                    submitSuccess = true;
					window.location.href = r.redirectURL;
				},
				complete: function() {
					if (!submitSuccess) {
				    	my.enableAutosave();
					}
					jQuery.fn.dialog.hideLoader();
				}
			}).submit();
		});

		ConcreteEvent.subscribe('PanelCloseDetail',function(e, panelDetail) {
			if (panelDetail && panelDetail.identifier == 'page-composer') {
				my.disableAutosave();
			}
		});

	    my.enableAutosave();
	}

}

$(function() {
	ConcretePageComposerDetail.start();
});
</script>
