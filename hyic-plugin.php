<?php
/**
 * Plugin Name: HYIC-Plugin
 */

include_once(dirname(__FILE__).'/custom-post-types/hyic_project.php');
include_once(dirname(__FILE__).'/custom-post-types/hyic_event.php');
include_once(dirname(__FILE__).'/custom-post-types/hyic_partner.php');
 

function hyic_setup_post_types() {
    hyic_setup_project_type();
    hyic_setup_event_type();
    hyic_setup_partner_type();
}

function hyic_unregister_post_types() {
    unregister_post_type('hyic_project');
    unregister_post_type('hyic_event');
    unregister_post_type('hyic_partner');
}

/**
 * Activate the plugin.
 */
function hyic_activate() { 
    // Trigger our function that registers the custom post type plugin.
    hyic_setup_post_types(); 
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules(); 
}
register_activation_hook( __FILE__, 'hyic_activate' );

function hyic_deactivate() {
    // Unregister the post type, so the rules are no longer in memory.
    hyic_unregister_post_types();
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'hyic_deactivate' );

?>