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
        $message .= __('Please choose atleast one project from project list', 'user-group-module');
    }
	
	if($error == 0) {			
		$result 	  = $this->doSetGroupRelationTable($grpId, $selectUsr, $gType);
		switch($result){
			case 0:
			case 1:
			case 2:
				$message = __('Project has been successfully added to selected Group', 'user-group-module');
				break;			
			case 3:
				$message = __('Error in insertion', 'user-group-module');
				break;
		} 		         
    }	
}



$allPosts 		= $this->doGetPostTableInfo('`ID`,`post_title`'); 
//$allPosts 		= $this->doGetAllProjectList_group();

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
					<th><?php echo __('Project List', 'user-group-module'); ?></th>
					<td>
						<select name="ugroup_list" id="ugroup_list" style="width:325px;">
							<option value=""><?php echo __('Choose Projects', 'user-group-module'); ?></option>
							<?php 
								foreach($allPosts as $post){ ?>
									<option value="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></option>
							<?php }	?>
						</select>
						<input type="hidden" name="gType"   id="gType"  value="group_project" />
					</td>
				</tr>				
				
				<tr id="show_users" style="display:none;">			
					<th><?php echo __('Group List', 'user-group-module'); ?> </th>
					<td></td>
				</tr>	

				<tr>
					<th></th>
					<td><img src="<?php echo GROUP_MODULE_URL; ?>images/AjaxLoading.gif" id="loader" /></td>					
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