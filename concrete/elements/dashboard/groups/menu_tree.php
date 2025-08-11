<?php

defined('C5_EXECUTE') or die("Access Denied.");

/** @var $urlHelper Url */
?>

<div class="row row-cols-auto g-0 align-items-center">

    <div class="btn-group me-3">
        <a href="<?=$view->url('/dashboard/users/groups')?>" class="btn btn-secondary p-2"><i class="fa fa-bars"></i></a>
        <a href="<?=$view->url('/dashboard/users/groups', 'view_tree')?>" class="btn btn-primary p-2"><i class="fa fa-sitemap"></i></a>
    </div>

</div>
