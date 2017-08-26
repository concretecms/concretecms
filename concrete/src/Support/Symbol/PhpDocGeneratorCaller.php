<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* Include this file to dump the currently defined vars */

$vars = get_defined_vars();
if (isset($this) && is_object($this)) {
    $vars['this'] = $this;
}
die('</script><pre>'.(new Concrete\Core\Support\Symbol\PhpDocGenerator())->describeVars($vars));
