<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<p><?php echo t('You are logged in as <strong>%s</strong>. You logged in on <strong>%s</strong>.', $uName, $uLastLogin)?></p>
<p><?php echo t('Last login')?>: <strong><?php echo $lastLoginSite?></strong></p>
<p><?php echo t('Last edit')?>: <?php if ($lastEditSite) { ?><strong><?php echo $lastEditSite?></strong><?php } else { ?><?=t('None')?><?php } ?></p>
<p><?=t('Total form submissions: <strong>%s</strong> (<strong>%s</strong> today). <a href="%s">View Form Results</a>.', $totalFormSubmissions, $totalFormSubmissionsToday, $view->url('/dashboard/reports/forms'))?></p>
