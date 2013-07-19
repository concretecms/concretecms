<?php  defined('C5_EXECUTE') or die('Access Denied');?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Bulk SEO Updater'), t('Manage Search Engine Optimization (SEO) Related Page Properties.'), false, false); 
$pageSelector = Loader::helper('form/page_selector');
$nh = Loader::helper('navigation');
$th = Loader::helper('text');
?>
<style type="text/css">
		.rowHolder {
			width: 869px;
			padding: 15px;
			position: relative;
		}
	
		.rowHolder div {
			float: left;
			min-height: 100px;
		}
		
		.rowHolder div.metaInput {
			float: none;
			margin: 0px;
			min-height: 0px;
		}
		.rowHolder div.headings {
			min-height: 50px;
		}
	
		.rowHolder.stripe {
			background: #eee;
		}
		
		.rowHolder.stripe .help-inline {
			color: #999;
		}
		
		
		.updateButton {
			float: right;
		}
		
		.headingsContainer {
			float: left;
			width: 150px;
			padding: 10px;
			border: 1px solid #ddd;
			border-radius: 10px;
		}
		
		.headings {
			float: left;
			clear: both;
		}
		
		.metaFieldContainer {
			float: left;
			width: 650px;
			padding-left: 30px;
		}
		
		.metaFieldContainer div {
			margin: 0px 10px 10px 10px;
		}
		
		div.updateButton {
			float: right;
			min-height: 0;
		}
		
		a.url-path {
			word-wrap: break-word;
			width: 300px;
			display: block;
		}
	</style>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#searchUnderParent').click(function(){
			$('#parentOptions').toggle();
			if ($('#searchUnderParent').hasClass('ccm-icon-option-closed')) {
				 	$(this).removeClass('ccm-icon-option-closed');
				 	$(this).addClass('ccm-icon-option-open');
			} else {
				$(this).removeClass('ccm-icon-option-open');
				$(this).addClass('ccm-icon-option-closed');
			}
			});
		});
		</script>
<form action="<?=$this->action('view')?>">
	<div class="ccm-pane-options">
			<label style="width: auto; margin-right: 1em; margin-left: 20px;"><?=t('Keywords'); ?></label><?php echo $form->text('keywords', '', array('style' => 'width: 130px')); ?><span style="margin-left: 30px;"><?=t(' # Per Page'); ?></span>
			<?=$form->select('numResults', array(
				'10' => '10',
				'25' => '25',
				'50' => '50',
				'100' => '100',
				'500' => '500'
			), Loader::helper('text')->specialchars($searchRequest['numResults']), array('style' => 'width:65px; margin: 0px 10px 0px 10px;'))?>
			<?php print $concrete_interface->submit(t('Search'), $formID, $buttonAlign = 'left', 'searchSubmit'); ?><br />
			<a href="javascript:void(0)" class="ccm-icon-option-closed" id="searchUnderParent"><?php echo t('Advanced Search'); ?></a>
			<div id="parentOptions" style="margin-left: 25px; display: <?php echo $parentDialogOpen ? 'block' : 'none'; ?>">
			<div id="pageSelectorHolder" style="float: left; width: 400px; margin-top: 15px;">
			<strong style="display: block; margin-top: 10px;"><?php echo t('Parent Page'); ?></strong>
			<?php print $pageSelector->selectPage('cParentIDSearchField', 'ccm_selectSitemapNode');?>
			</div>
			<div id="searchOptionHolder" style="width: 400px; margin-left: 65px; float: left; margin-top: 15px;">
				<br/><strong style="display: block;"><?=t('How Many Levels Below Parent?')?></strong><br/>
				<ul class="inputs-list" style="width: 130px; float: left;">
					<li><label><?=$form->radio('cParentAll', 0, false)?> <span><?=t('First Level')?></span></label></li>
					<li><label><?=$form->radio('cParentAll', 1, false)?> <span><?=t('All Levels')?></span></label></li>
				</ul>
				<div class="pageChecks"><label class="checkbox"> <?php echo $form->checkbox('noDescription', 1, $descCheck);  ?> <span><?=t(' No Meta Description'); ?></span></label></div>
				<div class="pageChecks"><label class="checkbox"> <?php echo $form->checkbox('noKeywords', 1, $keywordCheck);  ?> <span><?=t(' No Meta Keywords'); ?></span></label></div>
			</div>
				<div style="clear: both;"></div>
			</div>
	</div>
</form>

<div class="ccm-pane-body">
<?php
if (count($pages) > 0) {
	  $i = 0;
		foreach($pages as $cobj) {
			$cpobj = new Permissions($cobj);
			$i++;
			$cID = $cobj->getCollectionID();
			$stripe = ($i % 2?'stripe':'');
			?>
			<div class="ccm-results-list">
				<div class="rowHolder <?php echo $stripe; ?> ccm-seoRow-<?php echo $cID; ?>" style="float: left;">
					<form id="seoForm<?php echo $cID; ?>" action="<?php echo View::url('/dashboard/system/seo/page_data/', 'saveRecord')?>" method="post" class="pageForm">
						<div class="headingsContainer">
						
							<div class="headings">
								<?php echo $form->hidden('cID', $cID) ?>
								<strong><?php echo t('Page Name'); ?></strong>
								<br />
								<br />
								<?php echo $cobj -> getCollectionName() ? $cobj->getCollectionName() : ''; ?>
								<br />
								<br />
							</div>
						
							<div class="headings">
								<strong><?php echo t('Page Type'); ?></strong>
								<br />
								<br />
								<?php echo $cobj->getCollectionTypeName() ? $cobj->getCollectionTypeName() : t('Single Page'); ?>
								<br />
								<br />
							</div>
								
							<div class="headings"><strong><?php echo t('Modified'); ?></strong>
								<br />
								<br />
								<?php echo $cobj->getCollectionDateLastModified() ? $cobj->getCollectionDateLastModified() : ''; ?>
								<br />
								<br />
							</div>
								
						</div>
						
						
						<div class="metaFieldContainer">
							<div><strong><?php echo t('Meta Title'); ?></strong>
							<br />
							<br />
								<div class="metaInput">
									<?php $pageTitle = $cobj->getCollectionName();
									$pageTitle = htmlspecialchars($pageTitle, ENT_COMPAT, APP_CHARSET);
									$autoTitle = sprintf(PAGE_TITLE_FORMAT, SITE, $pageTitle);
									$titleInfo = array('title' => $cID);
									if(strlen($cobj->getAttribute('meta_title')) <= 0) {
										 $titleInfo[style] = 'background: whiteSmoke'; 
									}
									echo $form->text('meta_title', $cobj->getAttribute('meta_title') ? $cobj->getAttribute('meta_title') : $autoTitle, $titleInfo); 
									echo $titleInfo[style] ? '<br /><span class="help-inline">' . t('Default value. Click to edit.') . '</span>' : '' ?>
								</div>
							</div>
								
							<div style="margin-left: 30px;"><strong><?php echo t('Meta Description'); ?></strong>
							<br />
							<br />
								<div class="metaInput">
									<?php $pageDescription = $cobj->getCollectionDescription();
									$autoDesc = htmlspecialchars($pageDescription, ENT_COMPAT, APP_CHARSET);
									$descInfo = array('title' => $cID); 
									if(strlen($cobj -> getAttribute('meta_description')) <= 0) {
										$descInfo[style] = 'background: whiteSmoke'; 
									}
									echo $form->textarea('meta_description', $cobj->getAttribute('meta_description') ? $cobj->getAttribute('meta_description') : $autoDesc, $descInfo); 
									echo $descInfo[style] ? '<br /><span class="help-inline">' . t('Default value. Click to edit.') . '</span>' : '';
									 ?>
								</div>
							</div>
								
							<div>
								<strong><?php echo t('Meta Keywords'); ?></strong>
								<br />
								<br />
								<?php echo $form->textarea('meta_keywords', $cobj->getAttribute('meta_keywords'), array('title' => $cID)); ?>
							</div>
								
							<? if ($cobj->getCollectionID() != HOME_CID) { ?>
							
							<div style="margin-left: 30px;">
								<strong><?php echo t('Slug'); ?></strong>
								<br />
								<br />
								<?php echo $form->text('collection_handle', $cobj->getCollectionHandle(), array('title' => $cID, 'class' => 'collectionHandle')); ?>
								<br />
								<?php
									Page::rescanCollectionPath($cID);
									$path = $cobj->getCollectionPath();
									$tokens = explode('/', $path);
									$lastkey = array_pop(array_keys($tokens));
									$tokens[$lastkey] = '<strong class="collectionPath">' . $tokens[$lastkey] . '</strong>';
									$untokens = implode('/', $tokens);
									?><a class="help-inline url-path" href="<?php echo $nh->getLinkToCollection($cobj); ?>" target="_blank"><?php echo BASE_URL . DIR_REL . $untokens; ?></a><?php
								?>
							</div>
							<? } ?>
									
							<div class="updateButton">
								<br />
								<br />
								<?php print $concrete_interface->submit('Save', $formID, $buttonAlign = 'right', 'seoSubmit update' . $cID, array('title' => $cID)); ?>
							</div>
							<div>
								<img style="display: none; position: absolute; top: 20px; right: 20px;" id="throbber<?php echo $cID ?>"  class="throbber<?php echo $cID ?>" src="<?php echo ASSETS_URL_IMAGES . '/throbber_white_32.gif' ?>" />
							</div>
						</div>
					</form>
				</div>
			</div>
			<div style="clear: left"></div>	
		<?php } ?>
	<?php } else { ?>
		<div class="ccm-results-list-none"><?php echo t('No pages found.')?></div>
	<?php  }
	print $concrete_interface->button(t('Update All'), 'javascript:void(0)', $buttonAlign='right', $innerClass=null, $args = array('id'=>'allSeoSubmit'));
 	?>
	<div style="clear: left;"></div>
	<script type="text/javascript">
	$(document).ready(function() {
		var options = { 
			url: '<?php echo $this->action("saveRecord") ?>',
			dataType: 'json',
			success:function(res) {
				if(res.success) {
					var cID = res.cID;
					$('.throbber'+cID).hide();
					$('.ccm-seoRow-'+cID).animate({"background-color" : "#57A957" }, 500);
					$('.update'+cID).removeClass('success');
					$('.update'+cID).removeClass('valueChanged');
					$('.ccm-seoRow-'+cID+' .collectionPath').html(res.newPath);
					$('.ccm-seoRow-'+cID+' .collectionHandle').val(res.cHandle);
					if ($('.ccm-seoRow-'+cID).hasClass('stripe')) {
						$('.ccm-seoRow-'+cID).animate({"background-color" : "#eee" }, 500);
					} else {
						$('.ccm-seoRow-'+cID).animate({"background-color" : "#ffff" }, 500);
					}
				} else {
					alert('An error occured while saving.');
				}
			}
		};
		
		$('.rowHolder input[type="text"], .rowHolder textarea' ).change(function() { 
			var identifier =  $(this).attr('title');
			$('.seoSubmit[title= ' + identifier + ']').addClass('success').addClass('valueChanged'); 
		});
		
		$('.seoSubmit').click(function() { 
			var iterator = $(this).attr('title'); 
			$('#seoForm' + iterator).ajaxForm(options); 
			$('#throbber'+iterator).show();
		});
		
		$('#allSeoSubmit').click(function() {
			$('.valueChanged').click();
		});
		
		$('.metaInput').click(function(){
			$(this).children().css({'background' : 'white'});
			$(this).children('.help-inline').hide();
		})
	});
	
	</script>
	<?php $pageList->displaySummary(); ?>
</div>
<div class="ccm-pane-footer">
	<?php $pageList->displayPagingV2(); ?>
</div>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>