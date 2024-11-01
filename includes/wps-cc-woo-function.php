<?php
Class WPS_CC_WOO_Functions{
	
	public static function subscribe_order_data($order_id){
		$order = new WC_Order( $order_id );

		$key = get_option('wc_wps_constant_contact_woo_api_key') ? get_option('wc_wps_constant_contact_woo_api_key') : '';
		$token = get_option('wc_wps_constant_contact_woo_access_token') ? get_option('wc_wps_constant_contact_woo_access_token') : '';
		$list_id  = (get_option('wc_wps_constant_contact_woo_list_id')) ? get_option('wc_wps_constant_contact_woo_list_id') : '';
		if( $key != '' && $token != '' && $list_id != '' ){
			$result = WPS_CC_WOO_Functions::subscribe($key,$token,$list_id,$order->billing_email,$order->billing_first_name,$order->billing_last_name);
			if( $result ){
				update_post_meta( $order_id, 'constant_contact_id', $result);
			}
		}else{
			echo "Configuration error exists. Please check the API Token, Access Token from <a href='".admin_url('admin.php?page=wc-settings&tab=wps_constant_contact_woo')."' target='_blank;'>Debug Log</a> for details.</i> ";
			exit;
		}
	}

	
	public static function subscribe($api_key,$token,$list_id,$email,$first_name,$last_name) {
		
		$contact=  array();
		$contact = array(
							'email_addresses' => array(
								array('email_address' => $email )
							),
							'first_name' => $first_name, 
							'last_name' => $last_name,
							'lists' => array(
								array(
									'id' => $list_id
								)
							)
						);
		$curl = curl_init('https://api.constantcontact.com/v2/contacts?api_key='.rawurlencode($api_key).'&action_by=ACTION_BY_OWNER');
		$header = array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		//curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($contact));
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($curl);
		curl_close($curl);
		$res_arr = json_decode($response);

		$log = get_option('wps_cc_woo_log') ? get_option('wps_cc_woo_log') : '<br/><br/>---------------------------------<br/><br/>';


		$new_log = '<div class="wps_cc_woo_log_details"><p>'.$log.'@<b><u>'.date('l jS \of F Y h:i:s A').'</u></b><br/>'.$response.'<br/><br/>---------------------------------<br/><br/></p></div>';

		update_option('wps_cc_woo_log',$new_log);


		if($res_arr->id){
			return $res_arr->id;
		}
		else{
			return false;
		}
	}
	

}?>