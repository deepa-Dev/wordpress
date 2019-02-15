<?php

/*
Plugin Name: User Group Module
Plugin URI: 
Description: Creare group and make a relation between users and group
Version: 1.0
Author: Test
Author URI: https://test.com/
License:
License URI:
Text Domain: user-group-module
Domain Path:
*/

global $groupModulePluginBasePath, $grp_module;
$groupModulePluginBasePath    = plugin_dir_path(__FILE__);

define( 'GROUP_MODULE_VERSION',    	'1.0' );
define( 'GROUP_MODULE_THIS',          __FILE__ );   
define( 'GROUP_MODULE_ROOT_DIR',      plugin_dir_path( GROUP_MODULE_THIS ) );
define( 'GROUP_MODULE_DIR', 			trailingslashit(dirname(plugin_basename(__FILE__))) );
define( 'GROUP_MODULE_URL', 			plugin_dir_url(dirname(__FILE__)) . GROUP_MODULE_DIR );
define( 'GROUP_MODULE_INCLUDE_DIR',	GROUP_MODULE_ROOT_DIR . 'includes/' );
define( 'GROUP_MODULE_ADMIN_DIR',  	GROUP_MODULE_ROOT_DIR . 'admin/' );
define( 'GROUP_MODULE_LANG_DIR',   	GROUP_MODULE_ROOT_DIR . 'languages/' );

if (!defined('GROUP_MODULE_PLUGIN_PATH'))
	define( 'GROUP_MODULE_PLUGIN_PATH', trailingslashit(dirname(__FILE__)) );

if (!defined('GROUP_MODULE'))
	define( 'GROUP_MODULE', $wpdb->prefix .'group');

if (!defined('GROUP_RELATION'))
	define( 'GROUP_RELATION', $wpdb->prefix .'group_relation');

if (!defined('GROUP_USERS'))
	define( 'GROUP_USERS', $wpdb->prefix .'users');

if (!defined('GROUP_POSTS'))
	define( 'GROUP_POSTS', $wpdb->prefix .'posts');


require_once($groupModulePluginBasePath.'classes/class-group-module.php');
$grp_module = new Group_Module_User();

$lang    = get_bloginfo("language");
$cLang   = explode("-",$lang);
define( 'GROUP_ACTIVE_LANG', $cLang[0]);

if ( ! function_exists( 'group_module_load' ) ) :
	function group_module_load()	{
		add_action('plugins_loaded', 'start_group_module_plugin');
		add_action( 'init',          'group_module_init', 5 );
		register_activation_hook(__FILE__,'group_module_install');
		register_deactivation_hook(__FILE__,'group_module_uninstall');		
	}
endif;

group_module_load();

/** Plugin Activation **/
if ( ! function_exists( 'group_module_install' ) ) :
	function group_module_install() {
		global $wpdb;
				
		$table1 = $wpdb->prefix."group";		
		$structure1 = "CREATE TABLE IF NOT EXISTS $table1 (
		   `gID` int(11) NOT NULL AUTO_INCREMENT,
		   `group_name` varchar(255) NOT NULL,
		   `group_status` enum('dynamic','manual') NOT NULL DEFAULT 'dynamic',
		  PRIMARY KEY (`gID`)
		)";   
		$wpdb->query($structure1);	
		
		$dGroupList = array 
					(
						array("all loggedin Users","manual"),array("all Editors","manual")
					);   
		
		foreach($dGroupList as $group){ 
	
			$wpdb->insert($table1, array(
				'group_name'       	=> $group[0],
				'group_status'     	=> $group[1]
			));	
			$lastid = $wpdb->insert_id;
		}		
			
		$table2 = $wpdb->prefix."group_relation";		
		$structure2 = "CREATE TABLE IF NOT EXISTS $table2 (
		   `mID` int(11) NOT NULL AUTO_INCREMENT,
		   `element_id` bigint(20) NOT NULL,
		   `relation_ID` text NOT NULL,
		   `rel_type` varchar(255) NOT NULL,		   
		  PRIMARY KEY (`mID`)
		)";   
		$wpdb->query($structure2);	
		
	}
endif;		
		
		
/** Runs when the plugin is deactivated **/
if ( ! function_exists( 'group_module_uninstall' ) ) :
	function group_module_uninstall(){
		global $wpdb;
		
		$table1 	   =  $wpdb->prefix."group";	
		$structure1    = "DROP TABLE $table1"; 
		$wpdb->query($structure1);	

		$table2 	   =  $wpdb->prefix."group_relation";	
		$structure2    = "DROP TABLE $table2"; 
		$wpdb->query($structure2);	
		
	}
endif;

/** Plugin Startup **/
if ( ! function_exists( 'start_group_module_plugin' ) ) :
	function start_group_module_plugin() {
		
		// Load Module Config
		require_once GROUP_MODULE_INCLUDE_DIR . 'group-module-config.php';
		$grpConfig = new Group_Module_Config();
		$grpConfig->load();		
	}
endif;

/** Init **/
if ( ! function_exists( 'group_module_init' ) ) :
	function group_module_init()	{
		//init functionality done here
		add_action( 'admin_enqueue_scripts', 'group_module_enqueue_admin' );
	}		
endif;	

/** Enqueue Admin **/
if ( ! function_exists( 'group_module_enqueue_admin' ) ) :
	function group_module_enqueue_admin()	{	
	
		wp_register_script(
			'group-module-ajax',
			plugins_url( 'js/script-ajax.js', GROUP_MODULE_THIS )					
		);
		
		wp_localize_script( 
			'group-module-ajax', 
			'groupMdlCntAjax', 
			array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))
		);  
		
		wp_register_script(
			'group-module-relate',
			plugins_url( 'js/group_module.js', GROUP_MODULE_THIS )					
		);
		
		
		wp_register_style(
			'group-module-style',
			plugins_url( 'css/style.css', GROUP_MODULE_THIS ),
			false,
			'',
			'all'
		); 
	}
endif;
