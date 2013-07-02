<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<style type="text/css">
    table#googleMapBlockSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap; vertical-align:top ; padding-bottom:8px}
    table#googleMapBlockSetup td{ font-size:12px; vertical-align:top; padding-bottom:8px;}
    #ccm_googlemap_block_location.notfound { border-color: #b94a48; -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075); -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075); box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);}
</style> 
<div class="ccm-ui">
    <div class="ccm-block-fields">
        <table id="googleMapBlockSetup" width="100%" class="table"> 
            <tr>
                <th><? echo  t('Map Title')?>: <div class="note">(<? echo t('Optional')?>)</div></th>
                <td><input id="ccm_googlemap_block_title" name="title" value="<? echo $mapObj->title?>" maxlength="255" type="text" style="width:80%"></td>
            </tr>	
            <tr>
                <th><? echo t('Location')?>:</th>
                <td>
                    <input id="ccm_googlemap_block_location" name="location" value="<? echo $mapObj->location?>" maxlength="255" type="text" style="width:80%">
                    <input id="ccm_googlemap_block_latitude" type="hidden" name="latitude" value="<? echo $mapObj->latitude;?>" />
                    <input id="ccm_googlemap_block_longitude" type="hidden" name="longitude" value="<? echo $mapObj->longitude;?>" />
                    <div id="ccm_googlemap_block_note" class="note"><? echo t('Start typing a location (e.g. Apple Store or 235 W 3rd, New York) then click on the correct entry on the list.')?></div>
                    <div id="map-canvas"></div>
                </td>
            </tr>
            <tr>
                <th><? echo t('Zoom')?>:</th>
                <td>
                    <input id="ccm_googlemap_block_zoom" name="zoom" value="<? echo $mapObj->zoom?>" maxlength="255" type="text" style="width:80%">
                    <div class="ccm-note"><? echo t('Enter a number from 0 to 21, with 21 being the most zoomed in.')?> </div>
                </td>
            </tr>	
        </table>
    </div>
</div>