<?php
class Group_Module_User {
	
	public function Group_Module_User() {
		global $wpdb;			
	} 
	
	public function printAllArray($result){
		echo "<pre>";
		print_r($result, true);		
		echo "</pre>";		
	}
	
	//REPLACE ',' AS FIRST CHARACTER IN A STRING
	function replaceSpecialChar($string){
		if($string[0] == ','){
			$newStr = substr($string, 1);
		} else {
			$newStr = $string;
		}
		return $newStr;
	}
	
	//GET TABLE INFO
	public function doGetGroupTableInfo($strColumn='', $key='', $value='') {
		global $wpdb;	
		
		if($value !='') { $where = 'WHERE `'.$key.'` = "'.$value.'" '; } else $where ='';
		$strResult = $wpdb->get_results('SELECT '.$strColumn.' FROM '.GROUP_MODULE.' '.$where.' '); 
		return $strResult;	
	}
	
	//GET TABLE INFO BY FIELD
	public function doGetGroupTableRecord($strColumn='*') {
		global $wpdb;	
		
		$strResult = $wpdb->get_results('SELECT '.$strColumn.' FROM '.GROUP_MODULE.' ORDER BY `gID` ASC '); 		
		return $strResult;	
	}
	
	//GET USER TABLE INFO
	public function doGetUserTableInfo($strColumn='', $key='', $value='') {
		global $wpdb;	
		
		if($value !='') { $where = 'WHERE `'.$key.'` = "'.$value.'" '; } else $where =''; 
		$strResult = $wpdb->get_results('SELECT '.$strColumn.' FROM '.GROUP_USERS.' '.$where.' ORDER BY `display_name` ASC'); 
		return $strResult;	
	}
	
	//GET POST TABLE INFO
	public function doGetPostTableInfo($strColumn='', $key='', $value='') {
		global $wpdb;	
		
		if($value !='') { $where = ' AND `'.$key.'` = "'.$value.'" '; } else $where =''; 
		$strResult = $wpdb->get_results('SELECT '.$strColumn.' FROM '.GROUP_POSTS.' WHERE `post_type`= "pt-project" AND `post_status`= "publish" '.$where.' ORDER BY `post_title` ASC');
		return $strResult;	
	}
	
	
	//GET GROUP USER RELATION INFO
	public function doGetGroupUserRelationInfo($group, $gType) {
		global $wpdb;	
		
		$where 			= 'WHERE `element_id` = '.$group.' AND `rel_type` = "'.$gType.'" '; 
		$selRes 		= $wpdb->get_results('SELECT `relation_ID` FROM '.GROUP_RELATION.' '.$where.' '); 
		
		switch($gType){
			case 'group_user':
				$grpUsers 		= explode(",",$selRes[0]->relation_ID); 
				foreach($grpUsers as $user){
					if(!empty($user)){
						$userAry 		= $this->doGetUserTableInfo('`display_name`', 'ID', $user);  
						$userInfo[]		= array('id'=>$user, 'uname'=>$userAry[0]->display_name);
					}
				}
				return $userInfo;
			break;
			case 'group_project':
				$grpGrps 		= explode(",",$selRes[0]->relation_ID); print_r($grpGrps);
				foreach($grpGrps as $grp){
					if(!empty($grp)){
						$grpAry 		= $this->doGetGroupTableInfo('`group_name`', 'gID', $grp); 
						$grpInfo[]		= array('id'=>$grp, 'gname'=>$grpAry[0]->group_name);
					}
				}
				return $grpInfo;
			break;
		}		
		
	}
	
	//CHECK CONTENT ALREADY EXIST
	public function isGroupNameAlreadyExist($value) {
		global $wpdb;	
		
		$strContRes = $wpdb->get_results('SELECT COUNT(*) AS total FROM '.GROUP_MODULE.' WHERE `group_name` = "'.$value.'" ');		
		$strTotal   = $strContRes[0]->total;
		
		return $strTotal;
	}
	
	//CHECK CONTENT ALREADY EXIST
	public function isGroupRelationAlreadyExist($grpId, $gType) {
		global $wpdb;	
		
		$strRes 	= $wpdb->get_results('SELECT COUNT(*) AS total FROM '.GROUP_RELATION.' WHERE `element_id` = '.$grpId.' AND `rel_type` = "'.$gType.'" ');		
		$strTotal   = $strRes[0]->total;
		
		return $strTotal;
	}
	
	//UPDATE GROUP TABLE
	public function doSetGroupUpdateAction($id, $value){
		global $wpdb;

		$result = $wpdb->update(GROUP_MODULE, 
			array('group_name'=>$value),
			array('gID' => $id)
		);
		
		if(false === $result) { return 3; }
		else return $result;		
	}
	
	//INSERT GROUP
	public function doSetGroupInfoContent($value){
		global $wpdb;		
		
		$strTotal     = $this->isGroupNameAlreadyExist($value);	
		if(!empty($strTotal) && $strTotal == 1 ){	
			$res     = $this->doGetGroupTableInfo('gID', 'group_name', $value);	
			$result = $wpdb->update(GROUP_MODULE, 
				array('group_name'=>$value),
				array('gID' => $res[0]->gID)
			);
			
			if(false === $result) { return 3; }
			else return $result;
		} else {
			$wpdb->insert(GROUP_MODULE, array(
				'group_name'  	 => $value,
				'group_status'   => 'dynamic'
			));
			$lastid = $wpdb->insert_id;
			if(empty($lastid) OR $lastid < 1) { return 3; }
			else return 2;			
		}		
	}
	
	// GROUP RELATION USER
	public function doSetGroupRelationTable($grpId, $selectUsr, $gType){
		global $wpdb;	
		
		$rel_value	  = $this->replaceSpecialChar($selectUsr);
		$strTotal     = $this->isGroupRelationAlreadyExist($grpId, $gType);	
		if(!empty($strTotal) && $strTotal == 1 ){	
			$result = $wpdb->update(GROUP_RELATION, 
				array('relation_ID'=>$rel_value),
				array('element_id' => $grpId, 'rel_type' => $gType)
			);
			if(false === $result) { return 3; }
			else return $result;
		} else{
			$wpdb->insert(GROUP_RELATION, array(
				'element_id'    => $grpId,
				'relation_ID'   => $rel_value,
				'rel_type'   	=> $gType
			));
			$lastid = $wpdb->insert_id;
			if(empty($lastid) OR $lastid < 1) { return 3; }
			else return 2;	
		}		
	}
	
	//GET FRONTEND RESTRICTED PROS BY USER-GROUP RELATION
	public function doReturnFrontendProjectsByUser($data){
		global $wpdb;
		
		$user		= wp_get_current_user(); 
		$userID    	= $user->ID;
		$selLocate  = '';
		
		$usrLocate  = "LOCATE(CONCAT(',', ".$userID." ,','),CONCAT(',',relation_ID,',')) > 0";			
		$strRes 	= $wpdb->get_results('SELECT `element_id` FROM '.GROUP_RELATION.' WHERE '.$usrLocate.' AND `rel_type` = "group_user" ');	
		$lTotal		= count($strRes);
		$j=1; 
		foreach($strRes as $res){
			if($j < $lTotal) { $condition = " OR "; } else { $condition = ""; }								
			$selLocate .= "LOCATE(CONCAT(',', ".$res->element_id." ,','),CONCAT(',',relation_ID,',')) > 0".$condition;		
			$j++;			
		} 
				
		$locateQry  = "(".$selLocate.")";		
		$selRes 	= $wpdb->get_results('SELECT `element_id` FROM '.GROUP_RELATION.' WHERE '.$locateQry.' AND `rel_type` = "group_project" ');
		
		$selProjID  = '';
		$total		= count($selRes);
		$k=1;
		foreach($selRes as $proj){			
			if($k < $total) { $append = ","; } else { $append = ""; }
			$selProjID .= $proj->element_id.$append;
			$k++;
		} 
		$selProjs 	= explode(",", $selProjID);			
		
		return $selProjs;
	}
	
	public function getDynamicGroupProRelation($grpName, $projectID){
		global $wpdb;
		
		//DYNAMIC GROUP : 'all loggedin Users'
		$grpAry 		= $this->doGetGroupTableInfo('`gID`', 'group_name', $grpName);
		$grpID 			= $grpAry[0]->gID; //print_r($grpAry);
		
		$groupLocate    = "LOCATE(CONCAT(',', ".$grpID." ,','),CONCAT(',',relation_ID,',')) > 0";			
		$strRes 	    = $wpdb->get_results('SELECT COUNT(*) AS total FROM '.GROUP_RELATION.' WHERE '.$groupLocate.' AND `element_id`='.$projectID.' AND `rel_type` = "group_project" ');			
		$allowProjGrp   = $strRes[0]->total;
		
		return $allowProjGrp;
	}
	

	
}	
?>