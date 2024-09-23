<?php

defined('C5_EXECUTE') or die('Access Denied.');

?>

<link rel="stylesheet" type="text/css" href="<?=ASSETS_URL?>/api/swagger/swagger-ui.css">

<div id="swagger-ui"></div>
<script src="<?=ASSETS_URL?>/api/swagger/swagger-ui-bundle.js" charset="UTF-8"> </script>
<script src="<?=ASSETS_URL?>/api/swagger/swagger-ui-standalone-preset.js" charset="UTF-8"> </script>

<script>
    window.onload = function() {
        window.ui = SwaggerUIBundle({
            url: "<?=URL::to('/ccm/system/api/openapi.json')?>",
            dom_id: '#swagger-ui',
            deepLinking: true,
            validatorUrl: null,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            persistAuthorization: true,
            oauth2RedirectUrl: '<?=$oauth2RedirectUrl?>',
            layout: "StandaloneLayout"
        });
        <?php if (isset($clientKey)) { ?>
        window.ui.initOAuth({
            clientId: "<?=h($clientKey)?>"
        })
        <?php } ?>
    };
</script>
