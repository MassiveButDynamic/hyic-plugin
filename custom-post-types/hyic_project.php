<?php

function hyic_setup_project_type() {
    register_post_type('hyic_project',
        array(
            'labels'      => array(
                'name'          => __('Projekte', 'textdomain'),
                'singular_name' => __('Projekt', 'textdomain'),
            ),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'public'      => true,
            'has_archive' => true,
            'menu_icon'=>'dashicons-portfolio',
        )
    );
} 
add_action( 'init', 'hyic_setup_project_type' );


function hyic_add_project_tile_color_box()
{
    $screens = ['hyic_project'];
    foreach ($screens as $screen) {
        add_meta_box(
            'hyic_project_tile_color_box',           // Unique ID
            'Farben',  // Box title
            'hyic_project_tile_color_box_html',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}
add_action('add_meta_boxes', 'hyic_add_project_tile_color_box');
 
function hyic_project_tile_color_box_html($post)
{
    $value = get_post_meta($post->ID, '_hyic_project_tile_color', true);
    ?>
    <label for="hyic_project_tile_color">Farbe der Kachel:</label>
    <input type="color" name='hyic_project_tile_color' value="<?php echo $value;?>" />
    <?php
}

function hyic_project_save_postdata($post_id)
{
    if (array_key_exists('hyic_project_tile_color', $_POST)) {
        update_post_meta(
            $post_id,
            '_hyic_project_tile_color',
            $_POST['hyic_project_tile_color']
        );
    }
}
add_action('save_post', 'hyic_project_save_postdata');

?>