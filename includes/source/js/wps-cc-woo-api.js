var $ =jQuery.noConflict();
$(document).ready(function() {

    $data_html = '<tr valign="top">\
                    <th scope="row" class="titledesc">\
                      <label for="wc_wps_constant_contact_woo_bulk_notice"></label>\
                    </th>\
                    <td><p><u>Do you like to send bulk order data to Constant Contact account list? </u> <br/>Follow the path, WooCommrce Orders -> Check the orders -> Choose bulk action as "Send to ConstactContact" -> Apply.<br/><i>Please Note, this feature is available only with PREMIUM version. <a href="https://www.wpsuperiors.com/shop/constant-contact-for-woocommerce" target="_blank;">Get Premium Version.</a></i></p>';

    $data_html += '<tr valign="top">\
                    <th scope="row" class="titledesc">\
                      <label for="wc_wps_constant_contact_woo_support"></label>\
                    </th>\
                    <td><p>Need support ? Do not hesiatate to write an email at <b>support@wpsuperiors.com</b>. Its our previlege to help you.</p>';
    $(".form-table tbody").append($data_html);

    $data_html2 = '<p><div class="section_debug_log_title"><a href="javascript:void(0);"><p><b>Debug Log</b></p></a></div></p>';
    $(".submit").append($data_html2);

    $(".section_debug_log_title").click(function(){
	    $(".wps_cc_woo_log_details").slideToggle('slow');
	}); 
});