<?php

/**
 * Plugin Name: Host FontAwesome Locally
 * Plugin URI: https://organisch.digital
 * Description: Host FontAwesome locally for improved performance and DSGVO conformity.
 * Version: 1.0.0
 * Author: Maximilian Huhle
 * Author URI: https://organisch.digital
 * License: GPL2
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Enqueue FontAwesome stylesheet
if (!function_exists('host_fontawesome_local_enqueue_styles')) {

    function host_fontawesome_local_enqueue_styles()
    {
        wp_enqueue_style('fontawesome', plugin_dir_url(__FILE__) . '/fontawesome-6.5.1/css/fontawesome.min.css', array(), '6.5.1', 'all');
    }
    add_action('wp_enqueue_scripts', 'host_fontawesome_local_enqueue_styles');
}

// Remove FontAwesome from Elementor
if (is_plugin_active('elementor/elementor.php')) {
    // Stop Elementor from loading FontAwesome
    add_filter('elementor/frontend/should_enqueue_fontawesome', '__return_false');

    // Remove FontAwesome from Elementor
    if (!function_exists('host_fontawesome_local_remove_elementor')) {
        function host_fontawesome_local_remove_elementor()
        {
            wp_dequeue_style('font-awesome');
        }
        add_action('wp_enqueue_scripts', 'host_fontawesome_local_remove_elementor', 20);
    }
}

// Check if Elementor was updated

if (!function_exists('host_fontawesome_local_check_elementor_update')) {
    function host_fontawesome_local_check_elementor_update($upgrader_object, $options)
    {
        if ($options['action'] === 'update' && $options['type'] === 'plugin' && $options['plugins'][0] === 'elementor/elementor.php') {
            // Elementor was updated, check if code is still working
            if (!wp_style_is('fontawesome', 'enqueued')) {
                // Code is not working, send email notification
                $to = 'maximilian.huhle@hotmail.de';
                $subject = 'Host FontAwesome Locally - Code not working anymore';
                $message = 'The code for hosting FontAwesome locally is not working anymore.';
                wp_mail($to, $subject, $message);
            }
        }
    }
    add_action('upgrader_process_complete', 'host_fontawesome_local_check_elementor_update', 10, 2);
}
