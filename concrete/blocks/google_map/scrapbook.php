<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var string $title
 * @var string $location
 * @var float $latitude
 * @var float $longitude
 */
?>

<div><?php echo $title; ?></div>
<div><?php echo t('Location: %s', $location); ?></div>
<div><?php echo t('Latitude: %s', $latitude); ?></div>
<div><?php echo t('Longitude: %s', $longitude); ?></div>