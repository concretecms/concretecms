<?
defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<h2>Mode</h2>

<select name="mode">
	<option value="category_list" <? if ($mode == 'category_list') { ?> selected <? } ?>>Category List</option>
</select>

<h2>Display Topic/Posts Beneath:</h2>