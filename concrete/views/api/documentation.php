<?php

declare(strict_types=1);

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $clientKey
 * @var string $oauth2RedirectUrl
 * @var string $openapiJsonUrl
 */
?>

<link rel="stylesheet" type="text/css" href="<?= ASSETS_URL ?>/api/swagger/swagger-ui.css">

<div id="swagger-ui"></div>

<script src="<?= ASSETS_URL ?>/api/swagger/swagger-ui-bundle.js" charset="UTF-8"> </script>
<script src="<?= ASSETS_URL ?>/api/swagger/swagger-ui-standalone-preset.js" charset="UTF-8"> </script>

<script>
window.onload = function() {
    window.ui = SwaggerUIBundle({
        url: <?= json_encode($openapiJsonUrl) ?>,
        dom_id: '#swagger-ui',
        deepLinking: true,
        validatorUrl: null,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
        ],
        persistAuthorization: true,
        oauth2RedirectUrl: <?= json_encode($oauth2RedirectUrl) ?>,
        layout: 'StandaloneLayout',
    });
    window.ui.initOAuth({
        clientId: <?= json_encode($clientKey) ?>,
    });
};
</script>
