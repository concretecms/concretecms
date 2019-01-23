<?php

use Concrete\Core\Error\ErrorList\Error\HtmlAwareErrorInterface;
use Concrete\Core\Error\ErrorList\ErrorList;

defined('C5_EXECUTE') or die('Access Denied.');

// Arguments:

/* @var Exception|Concrete\Core\Error\ErrorList\ErrorList|string|string[]|mixed|null $error */
// the error(s) to display

/* @var string $format */
// - how to display the errors: 'block' to display them as in a DIV element (fallback: list items)

/* @var string|null $message */
// - an "info" message in HTML format

/* @var string|null $success */
// - a "success" message in HTML format

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
        $_htmlErrors = [];
        foreach ($_error as $e) {
            $_htmlErrors[] = $e instanceof HtmlAwareErrorInterface && $e->messageContainsHtml() ? (string) $e : nl2br(h($e));
        }
        if (isset($format) && $format == 'block') {
            ?>
            <div class="ccm-system-errors alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert">×</button>
                <?php
                foreach ($_htmlErrors as $e) {
                    ?><div><?= $e ?></div><?php
                }
                ?>
        	</div>
            <?php
        } else {
            ?>
            <ul class="ccm-system-errors ccm-error">
                <?php
                foreach ($_htmlErrors as $e) {
                    ?><li><?php echo $e ?></li><?php
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
