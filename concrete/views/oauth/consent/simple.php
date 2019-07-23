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

<div style="display:flex;flex-direction:column; flex: 1">
    <div style="flex: 1; flex-shrink: 1; display: flex; flex-direction: column; justify-content: center">
        <h3 class="scope-title text-center">"<strong><?= h($client->getName()) ?></strong>"</h3>
        <h4 class="scope-description text-center">
            <strong><?= t('This client would like to access the following data:') ?></strong>
        </h4>

        <div class="scopes" style="overflow:auto">
            <?php
            if ($additionalScopes) {
                ?>
                <p class="text-center"><?= Punic\Misc::join($additionalScopes) ?></p>
                <?php
            }

            if ($mainScopes) {
                ?>
                <dl class="center" style="border-bottom: solid 1px rgba(255,255,255,0.2)">
                    <?php
                    foreach ($mainScopes as $scope) {
                        ?>
                        <dd style="border: solid 1px rgba(255,255,255,0.2); border-width: 1px 0 0; padding: 10px">
                            <i class="fa fa-check-square"></i>
                            <span style="padding: 0 10px"><?= h($scope->getDescription()) ?></span>
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
    </div>
    <div>
        <div class="form-group">
            <button class="btn btn-success pull-right" name="authorize_client" value="1" style="margin: 10px 0">
                <?= t('Authorize') ?>
            </button>
        </div>
    </div>
</div>
