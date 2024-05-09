<?php

/**
 * Plugin Name: Custom Dashboard Logo
 * Plugin URI: https://codewp.ai
 * Description: This plugin replaces the WordPress logo in the dashboard with a custom logo selected from the media library, if set.
 * Version: 1.0
 * Author: Sekou Perry
 * Author URI: https://codewp.ai
 */

function codewp_custom_logo() {
    $logo_url = get_option('codewp_custom_logo_url');
    if (!empty($logo_url)) {
        global $wp_admin_bar;
        // Modify the existing WordPress logo node
        $wp_admin_bar->add_node(array(
            'id'    => 'wp-logo',
            'title' => '<img src="' . esc_url($logo_url) . '" alt="Custom Logo" style="height: 20px;">',
            'href'  => admin_url('about.php'),
        ));
    }
}
add_action('admin_bar_menu', 'codewp_custom_logo', 11);

function codewp_enqueue_media($hook_suffix) {
    if ('options-general.php' === $hook_suffix) {
        wp_enqueue_media();
        add_action('admin_footer', 'codewp_admin_footer_script');
    }
}
add_action('admin_enqueue_scripts', 'codewp_enqueue_media');

function codewp_custom_logo_settings() {
    add_settings_section('codewp_custom_logo_section', 'Custom Logo', 'codewp_custom_logo_section_callback', 'general');
    add_settings_field('codewp_custom_logo_url', 'Logo URL', 'codewp_custom_logo_url_callback', 'general', 'codewp_custom_logo_section');
    register_setting('general', 'codewp_custom_logo_url');
}
add_action('admin_init', 'codewp_custom_logo_settings');

function codewp_custom_logo_section_callback() {
    echo '<p>Select your custom logo for the WordPress dashboard.</p>';
}

function codewp_custom_logo_url_callback() {
    $logo_url = esc_attr(get_option('codewp_custom_logo_url'));
    echo '<input id="codewp_custom_logo_url" type="text" name="codewp_custom_logo_url" value="' . $logo_url . '" />';
    echo '<button class="button" id="codewp_custom_logo_button">Choose Logo</button>';
}

function codewp_admin_footer_script() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('#codewp_custom_logo_button').click(function(e) {
                e.preventDefault();
                var mediaUploader;
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose a Logo',
                    button: {
                        text: 'Choose Logo'
                    },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#codewp_custom_logo_url').val(attachment.url);
                });
                mediaUploader.open();
            });
        });
    </script>
    <?php
}
