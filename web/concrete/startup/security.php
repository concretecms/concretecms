<? 
defined('C5_EXECUTE') or die("Access Denied.");
function h($input)
{
    return SecurityHelper::escapeHTML($input);
}
