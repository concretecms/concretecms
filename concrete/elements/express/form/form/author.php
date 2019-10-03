<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Core::make("helper/form/user_selector");
$author = null;
if ($entry) {
    $author = $entry->getAuthor()->getUserID();
} else {
    $u = Core::make(Concrete\Core\User\User::class);
    $author = $u->getUserID();
}
?>

<div class="form-group">
    <?php if ($view->supportsLabel()) { ?>
        <label class="control-label"><?=$label?></label>
    <?php } ?>
    <?=$form->selectUser('author', $author);?>
</div>