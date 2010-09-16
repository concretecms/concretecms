<?php
global $c;
$title = $c->getCollectionName();
$author = $c->getVersionObject()->getVersionAuthorUserName();
$date = $c->getCollectionDatePublic('F j, Y');
?>

<h1><?php echo $title; ?></h1>
<p class="meta">Posted by <?php echo $author; ?> on <?php echo $date; ?></p>
