<?php

use Concrete\Core\Error\ErrorList\ErrorList;

defined('C5_EXECUTE') or die('Access Denied.');

if (isset($error) && $error) {
    $_error = [];
    if ($error instanceof Exception) {
        $_error[] = $error->getMessage();
    } elseif ($error instanceof ErrorList) {
        $_error = $error->getList();
    } elseif (is_array($error)) {
        $_error = $error;
    } elseif (is_string($error)) {
        $_error[] = $error;
    }
    if (count($_error) > 0) {
        if (isset($format) && $format == 'block') {
            ?>
            <div class="ccm-system-errors alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert">×</button>
                <?php
                foreach ($_error as $e) {
                    ?><div><?= nl2br(h($e)) ?></div><?php
                }
                ?>
        	</div>
            <?php
        } else {
            ?>
            <ul class="ccm-system-errors ccm-error">
                <?php
                foreach ($_error as $e) {
                    ?><li><?php echo nl2br(h($e)) ?></li><?php
                }
                ?>
            </ul>
            <?php
        }
    }
}

if (isset($message)) {
    ?><div class="alert alert-info"><a data-dismiss="alert" href="#" class="close">&times;</a> <?= $message ?></div><?php
}

if (isset($success)) {
    ?><div class="alert alert-success"><a data-dismiss="alert" href="#" class="close">&times;</a> <?= $success ?></div><?php
}
