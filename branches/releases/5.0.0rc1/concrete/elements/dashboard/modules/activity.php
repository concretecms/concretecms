You are logged in as <b><?php echo $uName?></b>. You logged in on <b><?php echo $uLastLogin?></b>. 
<ul class="ccm-dashboard-list">
<li>Number of visits since your previous login: <b><?php echo $uLastActivity?></b></li>
<li>Total Visits: <b><?php echo $totalViews?></b> <span style="color: #aaa">(Not including yours.)</span></li>
<li>Page Versions: <b><?php echo $totalVersions?></b></li>
<li>Last Edit: <b><?php echo $lastEditSite?></b></li>
<li>Last Login: <b><?php echo $lastLoginSite?></b></li>
<li>Pages in Edit Mode: <b><?php echo $totalEditMode?></b></li>
<li>Total Form Submissions: <a href="<?php echo $this->url('/dashboard/form_results/')?>"><b><?php echo $totalFormSubmissionsToday?></b> today</a> (<b><?php echo $totalFormSubmissions?></b> total)</li>

</ul>