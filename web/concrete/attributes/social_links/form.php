<? defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Sharing\SocialNetwork\Service as Service; ?>

<div class="ccm-social-link-attribute-wrapper">
<? for ($i = 0; $i < count($data['service']); $i++) { ?>
	<div class="ccm-social-link-attribute ">
        <div class="form-group">
            <select name="<?=$this->field('service')?>[]" class="ccm-social-link-service-selector form-control">
                <? foreach($services as $s) { ?>
                    <option value="<?=$s->getHandle()?>" data-icon="<?php echo $s->getIcon() ?>" <? if ($s->getHandle() == $data['service'][$i]) { ?> selected="selected" <? } ?>><?= $s->getDisplayName() ?></option>
                <? } ?>
            </select>
        </div>
        <div class="form-group">
            <span class="ccm-social-link-service-text-wrapper">
                <span class="ccm-social-link-service-add-on-wrapper">
                    <span class="add-on">
                       <?php
                       if(is_object(Service::getByHandle($data['service'][$i]))) {
                           echo Service::getByHandle($data['service'][$i])->getServiceIconHTML();
                       } ?>
                    </span>
                    <input name="<?=$this->field('serviceInfo')?>[]" type="text" value="<?=$data['serviceInfo'][$i]?>" />
                    <button type="button" class="ccm-social-link-attribute-remove-line btn btn-link"><i class="fa fa-trash-o"></i></button>
                </span>
            </span>
        </div>
	</div>
<? } ?>

</div>

<div>
	<button type="button" class="btn btn-small btn-primary ccm-social-link-attribute-add-service"><?=t("Add Link")?> <i class="fa fa-plus"></i></button>
</div>

<script type="text/javascript">$(function() { ConcreteSocialLinksAttribute.init(); });</script>