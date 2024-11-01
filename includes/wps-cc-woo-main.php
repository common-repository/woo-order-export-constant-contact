<?php
Class WPS_CC_WOO_Main{
	public function __construct()
	{
		define( 'WPS_CC_WOO_BASE', plugin_basename( __FILE__ ) );
		define( 'WPS_CC_WOO_DIR', plugin_dir_path( __FILE__ ) );
		define( 'WPS_CC_WOO_URL', plugin_dir_url( __FILE__ ) );
		define( 'WPS_CC_WOO_IMG', plugin_dir_url( __FILE__ ).'source/images' );
		define( 'WPS_CC_WOO_JS', plugin_dir_url( __FILE__ ).'source/js' );
		define( 'WPS_CC_WOO_CSS', plugin_dir_url( __FILE__ ).'source/css' );
		
		require 'wps-cc-woo-admin.php';
		new WPS_CC_WOO_Admin;


		require 'wps-cc-woo-function.php';
		new WPS_CC_WOO_Functions;

		
		

	}
	
	
}?>