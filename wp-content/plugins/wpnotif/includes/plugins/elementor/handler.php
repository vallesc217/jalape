<?php

namespace WPNotif_Compatibility\ElementorForms;


if (!defined('ABSPATH')) {
    exit;
}

ElementorForms::instance();

final class ElementorForms
{
    protected static $_instance = null;

    protected $_slug = 'wpnotif-phone';

    /**
     *  Constructor.
     */
    public function __construct()
    {
        $this->init_hooks();
    }

    private function init_hooks()
    {
        //add_action('elementor_pro/forms/field_types', array(&$this, 'add_field'));

    }

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function add_field($field_types)
    {
        $field_types[$this->_slug] = __('WPNotif Phone', 'wpnotif');
        return $field_types;
    }


}
