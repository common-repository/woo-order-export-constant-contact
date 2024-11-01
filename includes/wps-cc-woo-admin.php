<?php
class WPS_CC_WOO_Admin {
    public function __construct() {

    	add_action('admin_init',__CLASS__.'::add_css_js');

        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_wps_constant_contact_woo', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_wps_constant_contact_woo', __CLASS__ . '::update_settings' );

        add_action('admin_footer-edit.php', array($this, 'add_cc_import_bulk_actions'));

        add_action( 'wp_ajax_wps_send_single_order_to_cc', array($this,'wps_send_single_order_to_cc_callback' ));
        
    }

    
    public static function add_css_js(){

    	if( isset($_GET['tab']) && $_GET['tab'] == 'wps_constant_contact_woo' ){
	    	wp_enqueue_style('wps-cc-woo-admin-css',WPS_CC_WOO_CSS.'/admin-style.css',array(),'1.0.0');
	    	wp_enqueue_style( 'wps-cc-woo-admin-css' );

	    	wp_enqueue_script('wps-cc-woo-admin-js',WPS_CC_WOO_JS.'/wps-cc-woo-api.js',array('jquery'),'1.0.0');
	    	wp_enqueue_script( 'wps-cc-woo-admin-js' );
	    }
    }
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['wps_constant_contact_woo'] = __( 'Constant Contact', 'woocommerce-settings-tab-demo' );
        return $settings_tabs;
    }
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }
    public static function get_settings() {
        
        $wc_wps_constant_contact_woo_list_id  = (get_option('wc_wps_constant_contact_woo_list_id')) ? get_option('wc_wps_constant_contact_woo_list_id') : '';
        $wc_wps_constant_contact_woo_order_status  = (get_option('wc_wps_constant_contact_woo_order_status')) ? get_option('wc_wps_constant_contact_woo_order_status') : 'processing';



        $lists = self::get_cc_lists();

        $settings = array(
            'section_title' => array(
                'name'     => __( 'Constant Contact', 'wps-cc-woo-lan' ),
                'type'     => 'title',
                'desc'     => 'Put exact value into below fields and Save Changes, for adding the user details into your selected Constant Contact list',
                'id'       => 'wc_wps_constant_contact_woo_section_title',
            ),
            'order_status' => array(
                'name' => __( 'Order Status', 'wps-cc-woo-lan' ),
                'type' => 'select',
                'options' => array('processing' => 'Order Processing', 'completed' => 'Order Complete' ),
                'default' => 'processing',
                'value' => $wc_wps_constant_contact_woo_order_status,
                'desc' => __( 'On which order status automatic export process will take place. ( Available with Premium Version Only ) <a href="https://www.wpsuperiors.com/shop/constant-contact-for-woocommerce" target="_blank;">Get Premium Version.</a>', 'wps-cc-woo-lan' ),
                'id'   => 'wc_wps_constant_contact_woo_order_status'
            ),
            'api_key' => array(
                'name' => __( 'API Key', 'wps-cc-woo-lan' ),
                'type' => 'text',
                'desc' => __( 'How to get the API Key? <a href="https://www.wpsuperiors.com/knowledge-base/constantcontact-setup/" target="_blank;"> Click Here</a>', 'wps-cc-woo-lan' ),
                'id'   => 'wc_wps_constant_contact_woo_api_key'
            ),
            'access_token' => array(
                'name' => __( 'Access Token', 'wps-cc-woo-lan' ),
                'type' => 'text',
                'desc' => __( 'How to get the Access Token? <a href="https://www.wpsuperiors.com/knowledge-base/constantcontact-setup/" target="_blank;"> Click Here</a>', 'wps-cc-woo-lan' ),
                'id'   => 'wc_wps_constant_contact_woo_access_token'
            ),
            'list_id' => array(
                'name' => __( 'Lists', 'wps-cc-woo-lan' ),
                'type' => 'select',
                'options'=> $lists,
                'value' => $wc_wps_constant_contact_woo_list_id,
                'desc' => __( '<br/>Put proper API Key, Access Token and click Save Changes button. <br/>These lists are automatic captured from your Constant Contact account.<br/>Choose on which list you want to add the woocommerce users.<br/>( For FREE version only one list captured and can be used at a time.<br/>All lists of your Constant Contact account can be captured and used with PREMIUM version only.)<p style="display: inline-block; margin-top: 7%;"><a href="https://www.wpsuperiors.com/shop/constant-contact-for-woocommerce" target="_blank;">Get Premium Version.</a></p>', 'wps-cc-woo-lan' ),
                'id'   => 'wc_wps_constant_contact_woo_list_id'
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_wps_constant_contact_woo_section_end',
            ),

            'section_debug_log' => array(
                'type'     => 'title',
                'desc'     => get_option('wps_cc_woo_log'),
                'id'       => 'wc_wps_constant_contact_woo_section_debug_log',
            ),
        );
        return apply_filters( 'wc_wps_constant_contact_woo_settings', $settings );
    }
    public static function get_cc_lists(){
    	$key = get_option('wc_wps_constant_contact_woo_api_key') ? get_option('wc_wps_constant_contact_woo_api_key') : '';
		$token = get_option('wc_wps_constant_contact_woo_access_token') ? get_option('wc_wps_constant_contact_woo_access_token') : '';
		if( $key != '' && $token != '' ){
			$curl = curl_init('https://api.constantcontact.com/v2/lists?api_key='.$key);
			$header = array(
				'Authorization: Bearer '.$token
			);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($curl);
			curl_close($curl);
			$result = json_decode($response, true);

			if( $result && is_array($result) ) {
				$option = array();
				foreach( $result as $list ){
					if (is_array($list)) {
						if (array_key_exists('id', $list) && array_key_exists('name', $list)) {
							$key = $list['id'];
							$value = $list['name'];
							$option[$key] = $value;
                            break;
						}else{
							$option[0] =  'No list found';
						}

					}else{
						$option[0] =  'No list found';
					}
					
				}
			}
		}else{
			$option[0] =  'No list found';
		}
		return $option;
    }

    public static function add_cc_import_bulk_actions(){
        global $post_type;

        if ($post_type == 'shop_order' ) 
        {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    var imp = $('<option>').val('import_to_cc').text('<?php _e('Send to ConstantContact', 'woocommerce') ?>');
                    $('#bulk-action-selector-top').append(imp);

                    $(".wps_cc_sub_id_column .button-primary").on('click',function(){
                        var orderid = $(this).parent().attr('data-order-id');
                        if(!orderid )
                            return;
                        var parent = $(this).parent();
                        parent.html("<i>Please wait, sending...</i><br/><img src='<?php echo WPS_CC_WOO_IMG; ?>/loading.gif' />");
                        var data = {
                            'action': 'wps_send_single_order_to_cc',
                            'dataType': "html",
                            'orderid': orderid,
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                           parent.html(response);
                        });
                    });
                });
            </script>
            <?php
        }
    }
    
    public static function wps_send_single_order_to_cc_callback(){
        $order_id = $_POST['orderid'];
        WPS_CC_WOO_Functions::subscribe_order_data($order_id);
        echo "<img src='".WPS_CC_WOO_IMG."/tick.png' /><br><i>Done. View <a href='".admin_url('admin.php?page=wc-settings&tab=wps_constant_contact_woo')."' target='_blank;'>Debug Log</a> for details.</i>";
        die;
    }

}

function wpc_woo_cc_id_order_column( $columns ) {

    $columns['cc_id'] = __( 'ConstactContact Subscribe ID', 'woocommerce' );

    return $columns;
}
add_filter( 'manage_edit-shop_order_columns', 'wpc_woo_cc_id_order_column', 20 );


function wps_cc_woo_add_cc_id_column_content( $column ) {
    global $post;

    if (  $column == 'cc_id' ) {
        if( get_post_meta($post->ID,'constant_contact_id',true) ){
            echo get_post_meta($post->ID,'constant_contact_id',true);
        }else{
            ?>
            <div class="wps_cc_sub_id_column" data-order-id="<?php echo $post->ID; ?>">
                <a href="javascript:void(0)" class="button-primary">Send to ConstactContact</a>
            </div>
            <?php
        }
       
    }
}
add_action( 'manage_shop_order_posts_custom_column', 'wps_cc_woo_add_cc_id_column_content' );

function wps_cc_woo_add_order_profit_column_style() {

    $css = '.widefat .column-cc_id { text-align:center; }';
    wp_add_inline_style( 'woocommerce_admin_styles', $css );
}
add_action( 'admin_print_styles', 'wps_cc_woo_add_order_profit_column_style' );


?>