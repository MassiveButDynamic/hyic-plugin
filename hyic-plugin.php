<?php
/**
 * Plugin Name: HYIC-Plugin
 */

function hyic_setup_post_type() {
    register_post_type('hyic_project',
        array(
            'labels'      => array(
                'name'          => __('Projekte', 'textdomain'),
                'singular_name' => __('Projekt', 'textdomain'),
            ),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
                'public'      => true,
                'has_archive' => true,
        )
    );
} 
add_action( 'init', 'hyic_setup_post_type' );


function hyic_add_custom_box()
{
    $screens = ['hyic_project'];
    foreach ($screens as $screen) {
        add_meta_box(
            'hyic_test_box',           // Unique ID
            'Eine tolle neue meta box',  // Box title
            'wporg_custom_box_html',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}
add_action('add_meta_boxes', 'hyic_add_custom_box');
 
function wporg_custom_box_html($post)
{
    ?>
    <label for="wporg_field">Eine ganz tolle meta box</label>
    <select name="wporg_field" id="wporg_field" class="postbox">
        <option value="">Select something...</option>
        <option value="something">Something</option>
        <option value="else">Else</option>
    </select>
    <?php
}
 
/**
 * Activate the plugin.
 */
function hyic_activate() { 
    // Trigger our function that registers the custom post type plugin.
    hyic_setup_post_type(); 
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules(); 
}
register_activation_hook( __FILE__, 'hyic_activate' );

function hyic_deactivate() {
    // Unregister the post type, so the rules are no longer in memory.
    unregister_post_type( 'hyic_project' );
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'hyic_deactivate' );

?>