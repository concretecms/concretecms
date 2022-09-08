<?php
defined('C5_EXECUTE') or die('Access Denied.');
echo '<div class="ccm-block-content-edit-inline">';
echo app('editor')->outputPageInlineEditor('content');
echo '</div>';