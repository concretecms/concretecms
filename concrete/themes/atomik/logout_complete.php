<?php

defined('C5_EXECUTE') or die('Access Denied.');

?>


<div class="login-page">
    <div class="container">
        <div class="login-page-header">
            <div class="row">
                <div class="col-12">
                    <h2 class="login-page-title"><?=t('Sign Out');?></h2>
                </div>
            </div>
        </div>
        <div class="row login-page-content">
            <div class="col-12 text-center">
                <p><?=$logoutMessage?></p>

                <div class="mt-3">
                    <a href="<?=URL::to('/')?>" class="btn btn-secondary"><?=t('Back to Home')?></a>
                </div>
            </div>
        </div>
    </div>
</div>