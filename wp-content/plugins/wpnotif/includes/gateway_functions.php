<?php

defined('ABSPATH') || exit;


WPNotif_Gateway::instance();

class WPNotif_Gateway
{
    protected static $_instance = null;


    /**
     *  Constructor.
     */
    public function __construct()
    {
        $this->init_hooks();
    }

    private function init_hooks()
    {
        require_once 'gateway_list.php';
        require_once plugin_dir_path(__DIR__) . 'gateways/app/handler.php';

        add_filter('wpnotif_sms_gateways', array($this, 'add_gateways'), 100);

        add_filter('wpnotif_group_gateways_list', array($this, 'group_gateways_list'), 100);


    }

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function group_gateways_list($gateways)
    {
        $groups = array();

        $groups['starting_group'] = array();

        $default_group = __('Alphabetical Order');
        foreach ($gateways as $key => $gateway) {
            $group = isset($gateway['group']) ? $gateway['group'] : $default_group;

            $groups[$group][$key] = $gateway;
        }


        ksort($groups[$default_group]);
        return $groups;
    }


    public function add_gateways($smsgateways)
    {

        $custom_gateway = $this->custom_gateway();
        $sms_gateway = $this->sms_gateway();

        return array_merge($smsgateways, $custom_gateway, $sms_gateway);
    }


    public function sms_gateway()
    {

        $src = admin_url('admin-ajax.php');

        $data = array('nonce' => wp_create_nonce('wpnotif_qrcode'), 'action' => 'wpnotif_get_qrcode');
        $big_src = add_query_arg($data, $src);

        $data['preview'] = 1;
        $src = add_query_arg($data, $src);

        $key = WPNotif_App_Handler::instance()->get_data();

        return array(
            'sms_phone_app' => array(
                'value' => 901,
                'group' => esc_attr__('Mobile'),
                'label' => esc_attr__('SMS App'),
                'inputs' => array(
                    __('OneSignal App ID') => array('text' => true, 'name' => 'onesignal_app_id', 'default_value' => '', 'optional' => 1),
                    __('OneSignal REST API Key') => array('text' => true, 'name' => 'onesignal_rest_api_key', 'default_value' => '', 'optional' => 1),
                    __('Daily SMS Limit') => array('text' => true, 'name' => 'sending_frequency', 'default_value' => 100),
                    __('Phone Number') => array('select' => true, 'name' => 'phone_number', 'options' => array(__('with + and country code') => 1, __('with only country code') => 2, __('without country code') => 3)),
                    __('QR Code') => array('image' => true, 'name' => 'app_cred', 'label' => esc_attr__('App Setup QR', 'wpnotif'), 'src' => $src, 'preview_src' => $big_src),
                    __('Key') => array('text' => true, 'label' => esc_attr__('Key (for manual setup)', 'wpnotif'), 'name' => 'key', 'default_value' => $key, 'readonly' => 1, 'fix_value' => 1),
                    __('download') => array('link' => true, 'name' => 'download', 'label' => '', 'link_text' => esc_attr__('Download Android App', 'wpnotif'), 'href' => 'https://bridge.unitedover.com/download/?app=wpnotif.apk&type=download'),
                ),
            ),
        );
    }

    public function custom_gateway()
    {

        $placeholder = 'to:{to}, message:{message}, sender:{sender_id}';
        $desc = '<i>' . __('Enter Parameters separated by "," and values by ":"') . '</i><br />';
        $desc .= 'To : {to}<br /> Message : {message}<br /> Sender ID : {sender_id}';

        return array(
            'custom_gateway' => array(
                'value' => 900,
                'group' => esc_attr__('Custom Gateway'),
                'label' => esc_attr__('Custom'),
                'inputs' => array(
                    __('SMS Gateway URL') => array('text' => true, 'name' => 'gateway_url', 'placeholder' => 'https://www.example.com/send'),
                    __('HTTP Header') => array('textarea' => true, 'name' => 'http_header', 'rows' => 3, 'optional' => 1, 'desc' => esc_attr__('Headers separated by ","')),
                    __('HTTP Method') => array('select' => true, 'name' => 'http_method', 'options' => array('GET' => 'GET', 'POST' => 'POST')),
                    __('Gateway Parameters') => array('textarea' => true, 'name' => 'gateway_attributes', 'rows' => 6, 'desc' => $desc, 'placeholder' => $placeholder),
                    __('Send as Body Data') => array('select' => true, 'name' => 'send_body_data', 'options' => array('No' => 0, 'Yes' => 1)),
                    __('Encode Message') => array('select' => true, 'name' => 'encode_message', 'options' => array(__('URL Encode') => 1, __('No') => 0, __('Convert To Unicode') => 2)),
                    __('Phone Number') => array('select' => true, 'name' => 'phone_number', 'options' => array(__('with only country code') => 2, __('with + and country code') => 1, __('without country code') => 3)),
                    __('Sender ID') => array('text' => true, 'name' => 'sender_id', 'optional' => 1),
                ),
            ),
        );
    }
}