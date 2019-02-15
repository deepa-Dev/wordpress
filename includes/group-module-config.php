<?php
if ( ! defined( 'GROUP_MODULE_VERSION' ) ) exit;


if ( ! class_exists( 'Group_Module_Config' ) ) {
	class Group_Module_Config extends Group_Module_User{
		
		public $grp_setting;
		
		function __construct( $settings = array() ) {
			$grp_setting = '';
		}
		
		public function load() {	
			add_action( 'admin_head',       					array( $this, 'admin_header' ) );
			add_action( 'admin_menu', 	 						array( $this, 'groupModule_admin_config_menu' ) );
			add_filter( 'proFrontend-user-group-module', 		array( $this, 'doGetUserGroupFrontendPro' ), 10, 1);			
			add_action( 'wp_ajax_ajaxGroupModule_Config', 		array( $this, 'ajaxGroupModule_Config' ) );
		}		
		
		/** Admin Header **/
		public function admin_header() {
			$screen = get_current_screen();  			
			wp_enqueue_style( 'group-module-style' );
			wp_enqueue_script( 'group-module-ajax' );
			wp_enqueue_script( 'group-module-relate' );
		}
		
		/** Add Module Content Main Menu */
		function groupModule_admin_config_menu() {
			if ( ! current_user_can( 'administrator' ) )
			return;
			$tmp = basename(dirname(__FILE__));	
			add_menu_page(__('Group Module', 'user-group-module'), __('Group Module', 'user-group-module'), '',__FILE__,"", GROUP_MODULE_URL . 'images/group-icon.png'); 					
			add_submenu_page(__FILE__, __('Manage Group', 'user-group-module'), __('Manage Group', 'user-group-module'),'0','manage_group',array( $this, 'setGroup_config' ));
			add_submenu_page(__FILE__, __('User Group', 'user-group-module'), __('User Group', 'user-group-module'),'0','manage_ugrp',array( $this, 'setUserGroup_config' ));			
			add_submenu_page(null, __('Add Group', 'user-group-module'), __('Add Group', 'user-group-module'),'0','add_grp',array( $this, 'setInsertGroup_config' ));
			add_submenu_page(null, __('Edit Group', 'user-group-module'), __('Edit Group', 'user-group-module'),'0','edit_grp',array( $this, 'setUpdateGroup_config' ));
		}		

		function setGroup_config(){		
			if ( is_admin ) {
				include(GROUP_MODULE_ADMIN_DIR .'manage_group.php');		
			}
		}

		function setUserGroup_config(){		
			if ( is_admin ) {
				include(GROUP_MODULE_ADMIN_DIR .'manage_user_group.php');		
			}
		}
		
		
		
		function setInsertGroup_config(){		
			if ( is_admin ) {
				include(GROUP_MODULE_ADMIN_DIR .'add_group.php');		
			}
		}
		
		function setUpdateGroup_config(){		
			if ( is_admin ) {
				include(GROUP_MODULE_ADMIN_DIR .'edit_group.php');		
			}
		}
		
		public function doGetUserGroupFrontendPro($data){
			$strResult   = $this->doReturnFrontendProjectsByUser($data);	
			return $strResult;
		}		
		
		
		//SET AJAX Module Content
		function ajaxGroupModule_Config(){
			global $wpdb;
			
			if( isset( $_REQUEST['group'] ) ){				
				
				switch($_REQUEST['gType']) {
					case 'group_user':							
						$allUsers 		= $this->doGetUserTableInfo('`ID`,`display_name`'); 						
						$grpUsers 		= $this->doGetGroupUserRelationInfo($_REQUEST['group'], $_REQUEST['gType']);
						
						echo '<th>'.__('User List', 'user-group-module').'</th>';		
						echo '<td>';
						echo '<select id="select1" name="select1" multiple="multiple" class="groupSelect">';
						foreach($allUsers as $user) {
							echo '<option value="'.$user->ID.'">'.$user->display_name.'</option>';
						}
						echo '</select>';
						echo '<button type="button" class="grpBtnList" onClick="addGroupForm()" >'.__('Add', 'user-group-module').' &raquo;</button>';						
						echo '<button type="button" class="grpBtnList" onClick="removeGroupForm()" >&laquo; '.__('Remove', 'user-group-module').'</button>';
						
						echo '<select id="select2" name="select2" multiple="multiple" class="groupSelect">';
						foreach($grpUsers as $guser) {
							echo '<option value="'.$guser['id'].'">'.$guser['uname'].'</option>';
						}
						echo '</select>';
						
						echo '</td>';
					break;				
					
				
				}				
			}			
			die();
		}
		
	}	
}