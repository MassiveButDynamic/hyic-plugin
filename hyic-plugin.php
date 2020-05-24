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
            'Farben',  // Box title
            'wporg_custom_box_html',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}
add_action('add_meta_boxes', 'hyic_add_custom_box');
 
function wporg_custom_box_html($post)
{
    $value = get_post_meta($post->ID, '_hyic_project_tile_color', true);
    ?>
    <label for="hyic_project_tile_color">Farbe der Kachel:</label>
    <input type="color" name='hyic_project_tile_color' value="<?php echo $value;?>" />
    <?php
}

function wporg_save_postdata($post_id)
{
    if (array_key_exists('hyic_project_tile_color', $_POST)) {
        update_post_meta(
            $post_id,
            '_hyic_project_tile_color',
            $_POST['hyic_project_tile_color']
        );
    }
}
add_action('save_post', 'wporg_save_postdata');
 
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