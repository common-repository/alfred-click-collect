<?php
/**
 * Plugin Name:		alfred24 Click & Collect
 * Plugin URI: 		
 * Description:		ffer pickup to automated parcel lockers and to retails stores
 * Version:			1.1.7
 * Author:			Pakpobox
 * Text Domain:		alfredtext 
 * Domain Path:		/languages
 * License:			GPLv2
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.txt
 */
if(!defined('ABSPATH'))
die();

load_plugin_textdomain( 'alfredtext', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

if(! class_exists('AlfredPlugin')){
    class AlfredPlugin {
    /**
     * Constructor
     */
    public function __construct()
    {
        include ("constants/constants.php");
        $this->setup_actions();
        
        $this->updatePluginLanguage();
        //updateLanguage();

    }
    /**
     * Setting up Hooks
     */
    public function setup_actions()
    {
        //Main plugin hooks
        add_action( 'admin_menu', array ($this, 'my_admin_menu') );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ));
        add_action('woocommerce_after_shipping_rate',array($this,'addClickAndCollectWidget'),  10, 2 );
        
    }
    function getSelectedLanguage(){
        $locale = strval(get_locale());
        
        switch ($locale) {
    
        case "en_US":
            $selectedLanguage = "English";
            break;
        case "es_ES":
            $selectedLanguage = "Espanol";
            break;
        case "fr_FR":
            $selectedLanguage = "Francais";
            break;
        case 'it_IT':
            $selectedLanguage = "Italiano";
            break;
        case "pl_PL":
            $selectedLanguage = "Polski";
            break;
        case "pt_PT":
            $selectedLanguage = "Portugues";
            break;
        case "ja":
            $selectedLanguage = "日本語";
            break;
        case "zh_TW":
            $selectedLanguage = "繁體中文";
        case "zh_CN":
            $selectedLanguage = "繁體中文";
            break;
        default:
            $selectedLanguage = "English";
        }

        return $selectedLanguage;
    }
    function updatePluginLanguage(){
        
        $locale = strval(get_locale());
        $selectedLanguage = "";
        $defaultValuesAlfredLocker = "";
        $defaultValuesAlfredPoint = "";
    
        delete_option('alfred-locker-selected-lang');
    
        switch ($locale) {
    
            case "en_US":
            $selectedLanguage = "English";
            $defaultValuesAlfredLocker = "Collect At alfred24 Locker";
            $defaultValuesAlfredPoint = "Collect At alfred24 Point";
            break;
            case "es_ES":
            $selectedLanguage = "Espanol";
            $defaultValuesAlfredLocker = "Recoger en el armario de alfred24";
            $defaultValuesAlfredPoint = "Recoge en el punto alfred24";
            break;
            case "fr_FR":
            $selectedLanguage = "Francais";
            $defaultValuesAlfredLocker = "Retirer à la consigne alfred24";
            $defaultValuesAlfredPoint = "Retirer au point alfred24";
            break;
            case 'it_IT':
            $selectedLanguage = "Italiano";
            $defaultValuesAlfredLocker = "Ritira da alfred24 locker";
            $defaultValuesAlfredPoint = "Ritira da alfred24 point";
            break;
            case "pl_PL":
            $selectedLanguage = "Polski";
            $defaultValuesAlfredLocker = "Odbiór w punkcie alfred24";
            $defaultValuesAlfredPoint = "Odbiór w punkcie alfred24";
            break;
            case "pt_PT":
            $selectedLanguage = "Portugues";
            $defaultValuesAlfredLocker = "Retira no CliqueRetire E-box";
            $defaultValuesAlfredPoint = "Retira no CliqueRetire Point";
            break;
            case "ja":
            $selectedLanguage = "日本語";
            $defaultValuesAlfredLocker = "アルフレッド24ロッカーで収集";
            $defaultValuesAlfredPoint = "アルフレッド24ポイントで収集";
            break;
            case "zh_TW":
            $selectedLanguage = "繁體中文";
            $defaultValuesAlfredLocker = "alfred24智能柜取件";
            $defaultValuesAlfredPoint = "alfred24自提点取件";
            case "zh_CN":
            $selectedLanguage = "繁體中文";
            $defaultValuesAlfredLocker = "alfred24智能櫃取件";
            $defaultValuesAlfredPoint = "alfred24自提點取件";
            break;
            default:
            $selectedLanguage = "English";
            $defaultValuesAlfredLocker = "Collect At alfred24 Locker";
            $defaultValuesAlfredPoint = "Collect At alfred24 Point";
        }
    

        $shippingLanguageKeyAlfredPoint = "alfred-point-shipping-lang".$selectedLanguage;
        
        if(!get_option($shippingLanguageKeyAlfredPoint)){
            add_option($shippingLanguageKeyAlfredPoint, $defaultValuesAlfredPoint );
        //add_option('alfred-point-shipping-name', $_POST['alfred-point-shipping-name']);
        }

            $shippingLanguageKeyAlfredLocker = "alfred-locker-shipping-lang".$selectedLanguage;
        
        if(!get_option($shippingLanguageKeyAlfredLocker)){
            add_option($shippingLanguageKeyAlfredLocker, $defaultValuesAlfredLocker );
        //add_option('alfred-point-shipping-name', $_POST['alfred-point-shipping-name']);
        }
        



        if(!get_option('alfred-locker-selected-lang')){
        
            add_option('alfred-locker-selected-lang',  $selectedLanguage);
        
        
        
        
        }
        else{
            
            update_option('alfred-locker-selected-lang',$selectedLanguage);
        }



    }
    function addClickAndCollectWidget($method, $index){
        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
        $chosen_shipping = $chosen_methods[0]; 
        
        if (strpos($chosen_shipping, 'local_pickup') === 0 && strpos($method->get_id(), 'local_pickup') === 0)
        //if($chosen_shipping == "local_pickup:1" && $method->get_id() == "local_pickup:1")
        include ("pages/click-collect-widget.php");
    }
        
    function admin_enqueue_scripts()
    {
        
        $screen = get_current_screen();
        
        if (strpos($screen->id, "alfredplugin") !== false) {

            wp_enqueue_style('wocp-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
            wp_enqueue_script('ewarrant_admin', plugin_dir_url(__FILE__) . 'assets/js/admin.js', array('jquery'), null, true);
            
        
        }
        
    }

    function my_admin_menu() {
        add_menu_page( 'alfred24 Pick and Drop Plugin', 'alfred24 Click & Collect', 'manage_options', 'alfredplugin-settings', array ($this, 'add_admin_page'), 'dashicons-tickets', 6  );
    }

    function add_admin_page(){
        //include ("constants/constants.php");
        
        include ("pages/settings-admin-page.php");
        
        
    }
    
    

    }
    

    // add_filter( 'woocommerce_order_amount_total', 'custom_cart_total_final' );
    function custom_cart_total_final($order_total) {

        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
        $chosen_shipping = $chosen_methods[0]; 
        
        echo "custom cart total final";
        if(strpos($chosen_shipping, 'local_pickup') === 0 ){
            $lockerType = get_option('alfred-selected-locationtype');

            $lockerPrice = 0;
            
            if($lockerType == "ALFRED_LOCKER"){
                $lockerPrice = get_option('alfred-locker-shipping-rate');
                
            }
            else if($lockerType == "ALFRED_POINT") {
                $lockerPrice = get_option('alfred-point-shipping-rate');
            }
            // else if($lockerType == "HKPOST_LOCKER"){
            //     $lockerPrice = get_option('alfred-hkpost-shipping-rate');
            // }
            // else if($lockerType == "HKPOST_OFFICE"){
            //     $lockerPrice = get_option('alfred-hkpost-office-rate');
            // }
            else if($lockerType == "CIRCLE_K"){
                $lockerPrice = get_option('alfred-circle-k-rate');
            }
            else if($lockerType == "SEVEN_ELEVEN"){
                $lockerPrice = get_option('alfred-seven-eleven-rate');
            }
            
            $order_total += $lockerPrice;
            
        }

        return $order_total;
    }
    
    add_filter( 'woocommerce_thankyou_order_received_text', 'd4tw_custom_ty_msg' );

    function d4tw_custom_ty_msg ( $thank_you_msg ) {

        //str_replace("Local Pickup","Local Pickup", "alfred24 Click & Collect");
        return $thank_you_msg;
    }

    // Get the WC_Order object from the Order ID
    add_filter( 'woocommerce_cart_shipping_method_full_label', 'custom_shipping_method_labels', 10, 2 );
    function custom_shipping_method_labels( $label, $method ){
        
        if (strpos($method->get_id(), 'local_pickup') === 0)
            //$label = "alfred24 Click & Collect";

            if(!get_option('plugin_heading')){
            
                return "alfred24 Click & Collect";
            }
            else{
            
                $title = get_option('plugin_heading') ;
            

                return $title;
            
            
        }
        else{
            return $label;
        }
            
        
            //return $label;
    }
    add_filter( 'woocommerce_checkout_fields' , 'misha_labels_placeholders', 20 );

        function misha_labels_placeholders( $fields ) {
        
        unset($fields['billing']['billing_state']);
        return $fields;
        
    }

    add_filter( 'woocommerce_order_shipping_to_display', 'filter_email_shipping_text', 10, 2 );

        function filter_email_shipping_text( $shipping, $order_id ) {


        if($shipping == "Local pickup"){
            if(!get_option('plugin_heading')){
                return "alfred24 Click & Collect";
            }
            else{
            $title = get_option('plugin_heading') ;
            

                return $title;
            
            }
            
        }

        return $shipping;
        
    
    }
    add_action('woocommerce_thankyou', 'ceb_order_complete', 10, 1);
    function ceb_order_complete( $order_id ) {
        
        if ( ! $order_id )
        return;

        delete_option('alfred-selected-locationtype');

        WC()->customer->set_billing_address_1(wc_clean(''));
        WC()->customer->set_billing_address_2(wc_clean(''));
        WC()->customer->set_billing_city(wc_clean(''));
        WC()->customer->set_billing_state(wc_clean(''));


        WC()->customer->set_shipping_address(wc_clean(''));
        WC()->customer->set_shipping_address_2(wc_clean(''));
        WC()->customer->set_shipping_city(wc_clean(''));

        WC()->customer->set_shipping_state(wc_clean(''));


        WC()->customer->save();


        // Getting an instance of the order object
        $order = wc_get_order( $order_id );

        //echo "order is is".$order_id;
        
    
        
        $order_firstname = $order-> get_shipping_first_name();
        //echo "this my order".$order_firstname;
        $billing_phone  = $order->get_billing_phone();
        
        $token = signIn($billing_phone,$order_id, $order_firstname);
        

        if($order->is_paid() || $order->has_status('processing') || $order->has_status('completed')) {
            global $wpdb;
            $payID = WC()->session->get( 'payID' );
            if(!empty($payID)) {
        if(!$wpdb->update($wpdb->prefix."ceb_registrations", array("paid"=>1), array("payID"=>$payID))) {
            die("ERROR IN PAYMENT COMPLETE");
        }
        }
    } else {
    //die("WASNT PAID");
    }
}
    function themeslug_enqueue_script() {
        
        
        
            wp_enqueue_script( 'easywidgetjs', plugin_dir_url( __FILE__ ) . "pages/easywidgetSDK/easywidget.js", array('jquery'), null, true );
        
            $script_params = array( 'path' =>  plugin_dir_url( __FILE__ ) );

            wp_localize_script( 'easywidgetjs', 'scriptParams', $script_params );
        

    
    }

    function signIn($billing_phone, $order_id, $order_firstname){
        $username =  get_option('alfred-plugin-username');
        $password =  get_option('alfred-plugin-password');

    
        $url = ALFREDWIDGET_LOGIN_URL;
    
        $response = wp_remote_post( $url, array(
        'method' => 'POST',
        'headers' => array(
            "content-type"=> "application/json"
        ),
        'body' => 
            json_encode(
                array( 
                "username" => $username, 
                "password" => $password 
                )
            ),
        
        )
        );
    
        if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        echo "Something went wrong: $error_message";
        } else {
        
        $body =  json_decode($response["body"]);
        $token = $body->token;

        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
        $chosen_shipping = $chosen_methods[0]; 
        if(strpos($chosen_shipping, 'local_pickup') === 0 ){
            completeOrder($token, $billing_phone, $order_id,$order_firstname);
        }
                    

        //echo $token;
        if(!get_option('alfred-plugin-token-new')){
            add_option('alfred-plugin-token-new', $token);
            
        }
        else{
            update_option('alfred-plugin-token-new',$token);
            }

            return $token;
        }

        return "";

    }
    function completeOrder($token, $billing_phone, $order_id, $order_firstname){
        $url = ALFREDWIDGET_SHIPMENTS_URL;
    
        $response = wp_remote_post( $url, array(
        'method' => 'POST',
        'headers' => array(
            "content-type"=> "application/json",
            "Authorization" =>"Bearer ".$token
        ),
        'body' => 
            json_encode(
                array( 
                    array(
                    "serviceType" => "DELIVERY",
                    "trackingNumber"=> "000000".$order_id,
                    "orderId"=>$order_id,
                    "additionalTrackingNumber"=> "",
                    "referenceNumber"=>"",
                    "locationId"=>get_option('alfred-selected-locationID'),
                    "accessCode"=>"",
                    "recipientPhone"=> $billing_phone,
                    "recipientEmail"=> "",
                    "recipientAddress"=> "",
                    "recipientName"=> $order_firstname,
                    "note"=> "",
                    "charge"=> "0",
                    "weight"=> "2",
                    "senderPhone"=> "",
                    "senderEmail"=> "",
                    "senderAddress"=> "",
                    "senderName"=> ""
                    )
                
                )
            ),
        
        )
        );
        
        $response_json = json_decode($response["body"]);

        if($response_json->errorCode != 200)
        {
            echo('<span style="color:red">');
            echo($response_json->message);
            echo('</span>');
        }
        
        if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        echo "Something went wrong: $error_message";
        } else {
            $body =  json_decode($response["body"]);
        }
    }
    add_action( 'wp_enqueue_scripts', 'themeslug_enqueue_script' );


    // add_action( 'woocommerce_review_order_before_order_total', 'custom_cart_total' );
    // add_action( 'woocommerce_before_cart_totals', 'custom_cart_total' );
    function custom_cart_total() {
        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
        $chosen_shipping = $chosen_methods[0]; 
        
        //strpos($chosen_shipping, 'local_pickup') === 0 
        if(strpos($chosen_shipping, 'local_pickup') === 0 ){
            $lockerType = get_option('alfred-selected-locationtype');

            $lockerPrice = 0;
            
            if($lockerType == "ALFRED_LOCKER"){
                $lockerPrice = floatval (get_option('alfred-locker-shipping-rate'));
                
            }
            else if($lockerType == "ALFRED_POINT") {
                $lockerPrice = floatval (get_option('alfred-point-shipping-rate'));
            }
            // else if($lockerType == "HKPOST_LOCKER"){
            //     $lockerPrice = floatval (get_option('alfred-hkpost-shipping-rate'));
            // }
            // else if($lockerType == "HKPOST_OFFICE"){
            //     $lockerPrice = floatval (get_option('alfred-hkpost-office-rate'));
            // }
            else if($lockerType == "CIRCLE_K"){
                $lockerPrice = floatval (get_option('alfred-circle-k-rate'));
            }
            else if($lockerType == "SEVEN_ELEVEN"){
                $lockerPrice = floatval (get_option('alfred-seven-eleven-rate'));
            }

            WC()->cart->total += $lockerPrice;
        }
    }
    
    add_filter( 'woocommerce_package_rates', 'woocommerce_package_rates', 10, 2 );
    function woocommerce_package_rates($rates,$packages){
        $lockerPrice = 0;
        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
        $chosen_shipping = $chosen_methods[0]; 
        $lockerType = get_option('alfred-selected-locationtype');
        $cart_total = WC()->cart->cart_contents_total;
        
        $threshold = get_option($lockerType.'_THRESHOLD') ?: '';

        if($threshold == '' || floatval($cart_total) < $threshold){
            if(strpos($chosen_shipping, 'local_pickup') === 0 ){
                if($lockerType == "ALFRED_LOCKER"){
                    $lockerPrice = floatval(get_option('alfred-locker-shipping-rate'));
                }
                else if($lockerType == "ALFRED_POINT") {
                    $lockerPrice = floatval(get_option('alfred-point-shipping-rate'));
                }
                // else if($lockerType == "HKPOST_LOCKER"){
                //     $lockerPrice = floatval(get_option('alfred-hkpost-shipping-rate'));
                // }
                // else if($lockerType == "HKPOST_OFFICE"){
                //     $lockerPrice = floatval(get_option('alfred-hkpost-office-rate'));
                // }
                else if($lockerType == "CIRCLE_K"){
                    $lockerPrice = floatval(get_option('alfred-circle-k-rate'));
                }
                else if($lockerType == "SEVEN_ELEVEN"){
                    $lockerPrice = floatval(get_option('alfred-seven-eleven-rate'));
                }
            }
        }
        if($rates[$chosen_shipping] !=  null && strpos($chosen_shipping, 'local_pickup') === 0){
            $rates[$chosen_shipping]->cost = $lockerPrice;
        }
        // $rates[$chosen_shipping]->cost = $lockerPrice;
        file_put_contents('file.txt',print_r($rates,true));

        return $rates;
    }    

    new AlfredPlugin();
    }
?>