<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 

<script>
function registerTextShown(cb){
	if(cb.checked)
		 $('#registerTextWrap').css('display','block');
	else $('#registerTextWrap').css('display','none');
}
</script>

<div style="margin-bottom:16px;">
<input name="showRegisterLink" type="checkbox" value="1" <?=($bObj->showRegisterLink)?'checked':''?> onchange="registerTextShown(this)" onclick="registerTextShown(this)" /> 
<strong><?=t('Show Register Link') ?></strong>
</div>

<div id="registerTextWrap" style=" display:<?=($bObj->showRegisterLink)?'block':'none'?>; margin-top:16px; "> 
<input name="registerText" type="text" value="<?=$bObj->registerText?>" maxlength="255" />
</div>

<div class="ccm-spacer" style="margin-bottom:16px;"></div>