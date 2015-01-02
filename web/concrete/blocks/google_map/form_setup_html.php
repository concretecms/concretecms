<? defined('C5_EXECUTE') or die("Access Denied."); ?> 

<div class="ccm-google-map-block-container">
    <div class="col-xs-12">
        <div class="form-group">
            <? echo $form->label('title', t('Map Title (optional)'));?>
            <? echo $form->text('title', $mapObj->title);?>
        </div>

        <div id="ccm-google-map-block-location" class="form-group">
            <? echo $form->label('location', t('Location'));?>
            <? echo $form->text('location', $mapObj->location);?>
            <? echo $form->hidden('latitude', $mapObj->latitude);?>
            <? echo $form->hidden('longitude', $mapObj->longitude);?>
            <div id="block_note" class="note"><? echo t('Start typing a location (e.g. Apple Store or 235 W 3rd, New York) then click on the correct entry on the list.')?></div>
            <div id="map-canvas"></div>	
        </div>
    </div>

    <div class="col-xs-4">
        <div class="form-group">
            <? echo $form->label('zoom', t('Zoom'));?>
            <? 
                $zoomArray = array();
                for($i=0;$i<=21;$i++) {
                    $zoomArray[$i] = $i;
                }
            ?>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-search-plus"></i></span>
                <? echo $form->select('zoom', $zoomArray, $mapObj->zoom);?>
            </div>
        </div>
    </div>

    <div class="col-xs-4">	
        <div class="form-group"> 
            <? echo $form->label('width', t('Map Width'));?>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-arrows-h"></i></span>
                <? if(is_null($width) || $width == 0) {$width = '100%';};?> 
                <? echo $form->text('width', $width);?> 
            </div>
        </div>
    </div>

    <div class="col-xs-4">
        <div class="form-group">
            <? echo $form->label('height', t('Map Height'));?>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-arrows-v"></i></span>
                <? if(is_null($height) || $height == 0) {$height = '400px';};?>
                <? echo $form->text('height', $height); ?>
            </div>
        </div>
    </div>

    <div class="col-xs-4">
      <div class="form-group">
        <? echo $form->label('scrollwheel', t('Enable Scrollwheel'));?>
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-binoculars"></i></span>
          <? echo $form->checkbox('scrollwheel', 1, (is_null($scrollwheel) || $scrollwheel)); ?>
        </div>
      </div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    window.C5GMaps.init();
});
</script>