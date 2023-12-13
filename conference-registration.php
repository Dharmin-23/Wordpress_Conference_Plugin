<?php

/*
Plugin Name: Conference Registration
Description: Manage conference registrations and fees.
Version: 1.0
Author: Dharmin Mehta
*/

if(!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__).'/');
}

require_once(plugin_dir_path(__FILE__) . 'admin/database.php'); 


register_activation_hook(__FILE__, 'conference_registration_activate');
register_deactivation_hook(__FILE__, 'conference_registration_deactivate');

function conference_registration_activate()
{
    DBP_tb_create();
}

function conference_registration_deactivate()
{
    deactivate_conference_plugin();
}

require_once(plugin_dir_path(__FILE__) . 'admin/admin-front-end.php'); 
require_once(plugin_dir_path(__FILE__) . 'admin/admin-page.php'); 

function conference_registration_enqueue_scripts()
{

    wp_enqueue_style('conference-registration-style', plugin_dir_url(__FILE__) . 'css/styles.css', array(), '1.0', 'all');
    wp_enqueue_script('conference-registration-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), '1.0', true);
}

add_action('wp_enqueue_scripts', 'conference_registration_enqueue_scripts');
