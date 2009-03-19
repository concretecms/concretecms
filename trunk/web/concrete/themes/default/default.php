<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>

<div class="ccm-rating-display" style="display: block">
    <input class="star {split:2}" disabled="true" type="radio" value="0.5"/>
    <input class="star {split:2}" disabled="true" type="radio" value="1.0"/>
    <input class="star {split:2}" disabled="true" type="radio" value="1.5"/>
    <input class="star {split:2}" disabled="true" type="radio" value="2.0"/>
    <input class="star {split:2}" disabled="true" type="radio" value="2.5"/>
    <input class="star {split:2}" disabled="true" type="radio" value="3.0"/>
    <input class="star {split:2}" disabled="true" type="radio" value="3.5" checked="checked"/>
    <input class="star {split:2}" disabled="true" type="radio" value="4.0"/>
    <input class="star {split:2}" disabled="true" type="radio" value="4.5"/>
    <input class="star {split:2}" disabled="true" type="radio" value="5.0"/>
</div>
<div class="ccm-rating-active" style="display: none">
    <input class="star" name="ccm-rating-star" type="radio" value="1.0"/>
    <input class="star" name="ccm-rating-star" type="radio" value="2.0"/>
    <input class="star" name="ccm-rating-star" type="radio" value="3.0"/>
    <input class="star" name="ccm-rating-star" type="radio" value="4.0"/>
    <input class="star" name="ccm-rating-star" type="radio" value="5.0"/>
</div>

<script type="text/javascript">
$(function() {
	$('.ccm-rating-display').hover(function() {
		$('.ccm-rating-display').hide();
		$('.ccm-rating-active').show();
	}, function() {
	
	});
});
</script>
	<div id="central">
		<div id="sidebar">
			<?
			$as = new Area('Sidebar');
			$as->display($c);
			?>		
		</div>
		
		<div id="body">	
			<?
			
			$a = new Area('Main');
			$a->display($c);
			
			?>
		</div>
		
		<div class="spacer">&nbsp;</div>		
	</div>

<? $this->inc('elements/footer.php'); ?>
