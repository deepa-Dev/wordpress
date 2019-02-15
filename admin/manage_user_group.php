<?php
global $wpdb;

if(isset($_REQUEST['sbmt'])) { 
    $grpId		 = $_REQUEST['ugroup_list'];
    $selectUsr   = $_REQUEST['selResult'];
	$gType       = $_REQUEST['gType'];
	//print_r($_REQUEST); exit;
	
    $error = 0;    	
    if($grpId == '') {
        $error = 1;
        $message = __('Please select Group', 'user-group-module');
    }	
    if($selectUsr == '') {
        $error = 1;
        if($grpId == '') {
            $message .= '<br>';
        }
        $message .= __('Please choose atleast one user from user list', 'user-group-module');
    }
	
	if($error == 0) {			
		$result 	  = $this->doSetGroupRelationTable($grpId, $selectUsr, $gType);
		switch($result){
			case 0:
			case 1:
			case 2:
				$message = __('User has been successfully added to selected Group', 'user-group-module');
				break;			
			case 3:
				$message = __('Error in insertion', 'user-group-module');
				break;
		} 		         
    }	
}

//$strGroups 	  = $this->doGetGroupTableRecord('*');

$dGroups 		= $this->doGetGroupTableInfo('*', 'group_status', 'manual');
$mGroups 		= $this->doGetGroupTableInfo('*', 'group_status', 'dynamic');
//$strResults	  = $this->doReturnFrontendProjectsByUser(1); print_r($strResults);
//$aa = apply_filters( 'projectFrontend-user-group-module', 1); print_r($aa);
?>

<form  name="addUser" id="addUser" method="post" action="" onSubmit="submitGroupForm()">
	<div class="wrap">
    <h2><?php echo __('Manage User Group', 'user-group-module'); ?></h2>
		<table class="form-table" id="addbpro">
			<tbody>
				
				<?php if(!empty($message)) { ?>
				<tr>
					<td colspan="2">
						<div class="updated" id="message"><p><?php echo $message; ?>.</p></div>					  
					</td>
				</tr>
				<?php } ?>  							
				
				<tr>		
					<th><?php echo __('Group List', 'user-group-module'); ?></th>
					<td>
						<select name="ugroup_list" id="ugroup_list" style="width:325px;">							
							<option value=""><?php echo __('Choose Group', 'user-group-module'); ?></option>
							<optgroup label="Special Groups">
							<?php 
								foreach($dGroups as $dgrp){ ?>
									<option value="<?php echo $dgrp->gID; ?>"><?php echo $dgrp->group_name; ?></option>
							<?php }	?>
							</optgroup>
							
							<optgroup label="All Groups">
							<?php 
								foreach($mGroups as $mgrp){ ?>
									<option value="<?php echo $mgrp->gID; ?>"><?php echo $mgrp->group_name; ?></option>
							<?php }	?>
							</optgroup>
						</select>
						<input type="hidden" name="gType"   id="gType"  value="group_user" />
					</td>
				</tr>
				
				<tr id="show_users" style="display:none;">			
					<th><?php echo __('User List', 'user-group-module'); ?> </th>
					<td></td>
				</tr>	

				<tr>
					<th></th>
					<td><img src="<?php echo GROUP_MODULE_URL; ?>images/AjaxLoading.gif" id="loader" style="display:none;" /></td>					
				</tr>
				<input type="hidden" name="selResult" id="selResult"  value="" />
			</tbody>
		</table>
		<p class="submit">
		  <input type="submit" name="sbmt" id="sbmt" value="<?php echo __('Save', 'user-group-module'); ?>" class="add-new-h2" />		 
		  &nbsp;
		  <input type="button" name="cancel" onClick="history.go(-1);" class="add-new-h2" value="<?php echo __('Cancel', 'user-group-module'); ?>" />
		</p>
	</div>
</form>