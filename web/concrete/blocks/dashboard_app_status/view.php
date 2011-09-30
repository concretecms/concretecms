<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<h1><?=t('Welcome Back')?></h1>

<p><?=t('You are currently running concrete5 <strong>%s</strong>.', APP_VERSION)?></p>

<p><?=t('Total form submissions: <strong>%s</strong> (<strong>%s</strong> total).', $totalFormSubmissions, $totalFormSubmissionsToday)?></p>