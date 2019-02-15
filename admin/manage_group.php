<?php

global $wpdb, $manage_groups;

//Our class extends the WP_List_Table class, so we need to make sure that it's there
if ( !class_exists('WP_List_Table') ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( !class_exists('Manage_GroupList_Table') ) {
	require_once( GROUP_MODULE_PLUGIN_PATH . 'classes/class-manage-group-table.php' );
}

$grpTable = new Manage_GroupList_Table();

echo "<div class='wrap'><h2>" . __('Manage Groups', 'user-group-module') . "<a href='admin.php?page=add_grp' class='add-new-h2'>Add Group</a>";
echo "</h2>\r\n<br /></div>";

if ( (isset($_REQUEST['action']) && $_REQUEST['action'] === 'remove') ) {	
	$grpTable->delete_user_group($_REQUEST['gID']);
	$message = __('Group has been successfully removed', 'user-group-module');
}

 if(!empty($message)) { ?>
				<table><tr>
				  <td colspan="2"><div class="updated" id="message">
					  <p><?php echo $message; ?>.</p>
					</div></td>

				</tr></table>
<?php } 
				
$manage_groups = $grpTable->doGetManageGroupList(); 
$grpTable->prepare_items();

// show all manage_groups
echo "<form method=\"post\">\r\n";
echo "<table style=\"width: 100%; border:1px solid red;\">";
$grpTable->display();
echo "</table>";
echo "</form>\r\n";
	

?>