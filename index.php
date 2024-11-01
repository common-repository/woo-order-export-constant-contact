<?php
/*
Plugin Name: Woo Order Export To Constant Contact (FREE)
Plugin URI: https://www.wpsuperiors.com/woo-order-export-to-constant-contact
Description: Export order data from WooCommerce to your Constant Conatct account's email list.
Version: 1.2.1
Author: WPSuperiors
* WC requires at least: 3.4.6
* WC tested up to: 8.3.1
*/
if ( ! defined( 'ABSPATH' ) ) {
	wp_die('Please Go Back');
	exit;
}
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
add_action( 'init', 'wps_cc_woo_active_check_free' );
function wps_cc_woo_active_check_free() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        add_action( 'admin_notices', 'wps_cc_woo_active_check_activation_failed_free' );
        deactivate_plugins( plugin_basename( __FILE__ ) ); 
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
    else{
    	if( get_option('wps_woo_order_expo_to_cc') == 'premium' ){
	        add_action( 'admin_notices', 'wps_cc_woo_active_check_activation_failed_free2' );
	        deactivate_plugins( plugin_basename( __FILE__ ) ); 
	        if ( isset( $_GET['activate'] ) ) {
	            unset( $_GET['activate'] );
	        }
	    }else{
	        require 'includes/wps-cc-woo-main.php';
	        new WPS_CC_WOO_Main;
	    }
    }
}

function wps_cc_woo_active_check_activation_failed_free(){
    ?><div class="error"><p>Please Activate <b>WooCommerce</b> plugin, before you proceed to activate <b>Constant Contact For WooCommerce</b> Plugin.</p></div><?php
}

function wps_cc_woo_active_check_activation_failed_free2(){
    ?><div class="error"><p>It seems you already activated <b>Woo Order Export To Constant Contact (PREMIUM)</b> plugin, before you proceed to activate <b>Woo Order Export To Constant Contact (FREE)</b> plugin, deactivate <b>Woo Order Export To Constant Contact (PREMIUM)</b> plugin.</p></div><?php
}
if  (!extension_loaded('curl')) {
    add_action( 'admin_notices', 'wps_cc_woo_active_curl_disabled_free' );
    deactivate_plugins( plugin_basename( __FILE__ ) ); 
}

function wps_cc_woo_active_curl_disabled_free(){
	?><div class="error"><p>CURL is disabled on your server. Please contact with server provider to enable it.</p></div><?php
}

add_action('init','WPS_CC_WOO_Main_load_free');
function WPS_CC_WOO_Main_load_free(){
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),'wps_cc_woo_action_links_free' );
}

function wps_cc_woo_action_links_free($links){
	$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=wps_constant_contact_woo' ) . '">' . __( 'Settings') . '</a>',
			'<a href="https://www.wpsuperiors.com/woo-order-export-to-constant-contact">' . __( 'Get Premium Version') . '</a>',
			'<a href="https://www.wpsuperiors.com/knowledge-base/constantcontact-setup/">' . __( 'Documentation') . '</a>',
			'<a href="https://www.wpsuperiors.com/contact-us/">' . __( 'Get Support') . '</a>',
		);
	return array_merge( $plugin_links, $links );
}
register_activation_hook( __FILE__, 'wps_cc_exp_free_save_option' );

function wps_cc_exp_free_save_option(){
    if (FALSE === get_option('wps_woo_order_expo_to_cc') && FALSE === update_option('wps_woo_order_expo_to_cc',FALSE)) 
        add_option('wps_woo_order_expo_to_cc','free');

    if (FALSE === get_option('wps_woo_order_expo_to_cc') && FALSE === update_option('wps_woo_order_expo_to_cc',FALSE)) 
        add_option('wps_woo_order_expo_to_cc','1.0.0');
}

register_deactivation_hook( __FILE__, 'wps_cc_exp_free_delete_option' );
function wps_cc_exp_free_delete_option(){
    delete_option('wps_woo_order_expo_to_cc');
    delete_option('wps_woo_order_expo_to_cc_ver');
}