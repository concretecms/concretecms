<?php
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$token = $app->make(\Concrete\Core\Validation\CSRF\Token::class);

$mainScopes = [];
$additionalScopes = [];
foreach ($auth->getScopes() as $scope) {
    if (!$scope->getDescription()) {
        $additionalScopes[] = h($scope->getIdentifier());
    } else {
        $mainScopes[] = $scope;
    }
}
?>

<div>
    <h3 class="scope-title text-center">"<strong><?= h($client->getName()) ?></strong>"</h3>
    <h4 class="scope-description text-center">
        <strong><?= t('This client would like to access the following data:') ?></strong>
    </h4>

    <div class="scopes">
        <?php
        if ($additionalScopes) {
            ?>
            <p class="text-center"><?= Punic\Misc::join($additionalScopes) ?></p>
            <?php
        }

        if ($mainScopes) {
            ?>
            <dl class="mb-3 mt-3">
                <?php
                foreach ($mainScopes as $scope) {
                    ?>
                    <dd class="mt-3">
                        <i class="fas fa-check-square"></i>
                        <span><?= h($scope->getDescription()) ?></span>
                    </dd>
                    <?php
                }
                ?>
            </dl>
            <?php
        }
        ?>
    </div>

    <?php $token->output('oauth_authorize_' . $client->getClientKey()); ?>

    <div class="form-group d-grid">
        <button class="btn btn-success w-100" name="authorize_client" value="1" style="margin: 10px 0">
            <?= t('Authorize') ?>
        </button>
    </div>
</div>
