<?php
global $wpdb;

if(isset($_REQUEST['sbmt'])) { 
    $grpName     = $_REQUEST['grp_name'];
    
	//print_r($_REQUEST); exit;
	
    $error = 0;    	
    if($grpName == '') {
        $error = 1;
        $message = __('Please add group name', 'user-group-module');
    }	
    
	if($error == 0) {			
		$result 	  = $this->doSetGroupUpdateAction($_REQUEST['gID'], $grpName); 							
		switch($result){
			case 0:
			case 1:
				$message = __('Group has been successfully updated', 'user-group-module');
				break;			
			case 3:
				$message = __('Error in insertion', 'user-group-module');
				break;
		} 		         
    }   
	
}

$strResult 	  = $this->doGetGroupTableInfo('group_name', 'gID', $_REQUEST['gID']); 

?>

<form  name="addUser" id="addUser" method="post" action="">
	<div class="wrap">
		<h2><?php echo __('Edit Group', 'user-group-module'); ?>
			<a href='admin.php?page=manage_group' class='add-new-h2'>Manage Group</a>
		</h2><br />
		
		<table class="form-table" id="addbpro">
			<tbody>
				
				<?php if(!empty($message)) { ?>
				<tr>
				  <td colspan="2"><div class="updated" id="message">
					  <p><?php echo $message; ?>.</p>
					</div></td>
				</tr>
				<?php } ?>  
				
				<tr>
					<th><?php echo __('Group Name', 'user-group-module'); ?> </th>
					<td><input type="text" name="grp_name" id="grp_name" value="<?php echo $strResult[0]->group_name; ?>" /></td>
				</tr>			
				
			</tbody>
		</table>
		<p class="submit">
		  <input type="submit" name="sbmt" id="sbmt" value="<?php echo __('Update', 'user-group-module'); ?>" class="add-new-h2" />		 
		  &nbsp;
		  <input type="button" name="cancel" onClick="history.go(-1);" class="add-new-h2" value="<?php echo __('Cancel', 'user-group-module'); ?>" />
		</p>
	</div>
</form>