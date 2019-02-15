<?php

class Manage_GroupList_Table extends WP_List_Table {	
	
	/**
    * Constructor, we override the parent to pass our own arguments
    * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
    */ 
	function __construct() {
       parent::__construct( array(
      'singular'=> 'manage_group', //Singular label
      'plural' => 'manage_groups', //plural label, also this well be one of the table css class
      'ajax'   => false //We won't support Ajax for this table
      ) );
    }
	
	function column_default($item, $column_name) {	 
		switch($column_name) {			
			case 'group_name':					
				return $item[$column_name]; 
			default:
				return print_r($item,true);
		}	
	}
	
	function column_group_name($item) {	 
		$edit   = __("Edit", "user-group-module");
		$delete = __("Delete", "user-group-module");
		$actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&gID=%s">'.$edit.'</a>','edit_grp','edit',$item['g_id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&gID=%s">'.$delete.'</a>',$_REQUEST['page'],'remove',$item['g_id']),
        );

		return sprintf('%1$s %2$s', $item['group_name'], $this->row_actions($actions) );	
	}
	
	function column_cb($item) {
		return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['group_name']);
	}
	
	/**
	* Define the columns that are going to be used in the table
	* @return array $columns, the array of columns to use with the table
	*/	
	function get_columns() {
		$columns = array(				
				//'cb'	=> '<input type="checkbox" />',						
				'group_name' => _x('Name of the Group', 'column name', 'user-group-module')				
			);
		return $columns;
	}
	
	/**
	* Decide which columns to activate the sorting functionality on
	* @return array $sortable, the array of columns that can be sorted by the user
	*/	
	function get_sortable_columns() {		
		$sortable_columns = array(	
			'group_name'	=> array('group_name', false)
		);		
		return $sortable_columns;
	}	
	
	
	function get_bulk_actions() {		
		
		$actions = array(			
			'delete'	=> __('Delete', 'user-group-module')	
		);
		
		return $actions;		
	}
	
	public function doGetManageGroupList($gID='') {
		global $wpdb;			
		
		$swhere = '';
		if($gID !='') { $swhere = ' AND gID = "'.$gID.'" '; }		
		$strGroups  = $wpdb->get_results('SELECT * FROM '.GROUP_MODULE.' WHERE group_status="dynamic" '.$swhere.' ORDER BY gID ASC');
		$listAry 	= array(); 
		foreach($strGroups as $group){			
			$listAry[] = array('g_id'=>$group->gID, 'group_name'=>$group->group_name);			
		}		
		return $listAry;
	}	
		
	function delete_user_group($value) {
		global $wpdb;
		
		if ( ! current_user_can( 'administrator' ) )
        return;
					
		$wpdb->query("DELETE FROM ".GROUP_MODULE." WHERE `gID` = '".$value."' ");
	} 
	
	function prepare_items() {
		global $manage_groups;
		
		$per_page  = 25;		

		$columns   = $this->get_columns();
		$hidden    = array();
		$sortable  = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$data = array();		
		foreach((array)$manage_groups as $manage_group) {	
			$data[] = array('g_id' => $manage_group['g_id'],'group_name' => $manage_group['group_name']);
		}		

		function usort_reorder($a,$b) {
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'g_id';
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'ASC';
			$result = strcasecmp($a[$orderby], $b[$orderby]);
			return ($order==='asc') ? $result : -$result;
		}
		usort($data, 'usort_reorder');

		if ( isset($_POST['what']) ) {
			$current_page = 1;
		} else {
			$current_page = $this->get_pagenum();
		}
		$total_items = count($data);
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items'	=> $total_items,
			'per_page'		=> $per_page,
			'total_pages'	=> ceil($total_items/$per_page)
		) );
	}	
}
?>