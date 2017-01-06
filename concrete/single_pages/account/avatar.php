<?php defined('C5_EXECUTE') or die("Access Denied.");

$save_url = \Concrete\Core\Url\Url::createFromUrl($view->action('save_thumb'));
$save_url = $save_url->setQuery(array(
    'ccm_token' => \Core::make('token')->generate('avatar/save_thumb'),
));
?>

<h2><?=$c->getCollectionName()?></h2>

	<p><?php echo t('Change the picture attached to my posts.')?></p>

		<div id="profile-avatar">
			<?php echo t('You need the Adobe Flash plugin installed on your computer to upload and crop your user profile picture.')?>
			<br /><br />
			<a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Download the Flash Player here</a>.
		</div>
		<?php if ($profile->hasAvatar()) { ?>
			<form method="post" action="<?php echo $view->action('delete')?>">
				<?=Core::make('token')->output('delete_avatar')?>
				<button type="submit" class="btn btn-danger"><?php echo t('Remove your user avatar')?> <i class="fa fa-trash icon-white"></i></button>
			</form>
		<?php } ?>

		<script type="text/javascript">
		ThumbnailBuilder_onSaveCompleted = function() {
			window.location.href="<?php echo $view->url('/account/avatar', 'saved')?>";
		};

		/* <?php /* flashvars - options for the avatar/thumb picker
        upload -- whether to enable or disable the upload button (default is "true")
        webcam -- whether to enable or disable the webcam button (default is "true")
        format -- whether to use "jpg", "png" or "auto" when encoding (default is "auto")
        imagePath -- which image should be preloaded into the editor
        overlayPath -- an optional image to provide chrome over the top of thumbnails
        redirectPath -- an optional path to redirect to once an image has been saved
        savePath -- an optional path for saving images ($_POST["thumbnail"], base64) instead of saving locally
        rounding -- an optional amount of pixel rounding on thumbnail edges (will output transparent corners)
        width -- the width of the thumbnail
        quality -- the quality of the output image when using the JPEG encoder (default is 80)
        height -- the height of the thumbnail
        backgroundColor -- the color to use when tinting the background of the editor (default is 0xFFFFFF)
        tint -- the amount of strength to apply when tinting the background of the editor (default is 0)
        */ ?> */

		$(function(){
			var params = {
				bgcolor: "#ffffff",
				wmode:  "transparent",
				quality:  "high"
			};
			var flashvars = {
				width: '<?php echo Config::get('concrete.icons.user_avatar.width') ?>',
				height: '<?php echo Config::get('concrete.icons.user_avatar.height') ?>',
				image: '<?php echo $profile->getUserAvatar()->getPath()?>',
				save: "<?php echo $save_url ?>"
			};
			swfobject.embedSWF ("<?php echo ASSETS_URL_JAVASCRIPT?>/thumbnail-editor-3.swf", "profile-avatar", "500", "400", "10,0,0,0", "includes/expressInstall.swf", flashvars, params);


	   });
		</script>

    <br/>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/account')?>" class="btn btn-default" /><?=t('Back to Account')?></a>
        </div>
    </div>
