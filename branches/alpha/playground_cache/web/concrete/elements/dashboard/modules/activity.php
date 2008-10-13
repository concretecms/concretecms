<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
You are logged in as <b><?=$uName?></b>. You logged in on <b><?=$uLastLogin?></b>. 
<ul class="ccm-dashboard-list">
<li>Number of visits since your previous login: <b><?=$uLastActivity?></b></li>
<li>Total Visits: <b><?=$totalViews?></b> <span style="color: #aaa">(Not including yours.)</span></li>
<li>Page Versions: <b><?=$totalVersions?></b></li>
<li>Last Edit: <b><?=$lastEditSite?></b></li>
<li>Last Login: <b><?=$lastLoginSite?></b></li>
<li>Pages in Edit Mode: <b><?=$totalEditMode?></b></li>
<li>Total Form Submissions: <a href="<?=$this->url('/dashboard/form_results/')?>"><b><?=$totalFormSubmissionsToday?></b> today</a> (<b><?=$totalFormSubmissions?></b> total)</li>

</ul>