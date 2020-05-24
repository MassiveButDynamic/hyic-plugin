<?php

function hyic_setup_partner_type() {
    register_post_type('hyic_partner',
        array(
            'labels'      => array(
                'name'          => __('Partner', 'textdomain'),
                'singular_name' => __('Partner', 'textdomain'),
            ),
            'supports' => array('title', 'thumbnail'),
            'public'      => true,
            'has_archive' => true,
            'menu_icon'=>'dashicons-groups',
        )
    );
} 
add_action( 'init', 'hyic_setup_partner_type' );


function hyic_add_partner_link_box()
{
    $screens = ['hyic_partner'];
    foreach ($screens as $screen) {
        add_meta_box(
            'hyic_partner_link_box',           // Unique ID
            'Link',  // Box title
            'hyic_partner_link_box_html',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}
add_action('add_meta_boxes', 'hyic_add_partner_link_box');
 
function hyic_partner_link_box_html($post)
{
    $value = get_post_meta($post->ID, '_hyic_partner_link', true);
    ?>
    <label for="hyic_partner_link">Link zur Website des Partners:</label><br><br>
    <input type="text" name='hyic_partner_link' value="<?php echo $value;?>" style='width: 100%;'/>
    <?php
}

function hyic_partner_save_postdata($post_id)
{
    if (array_key_exists('hyic_partner_link', $_POST)) {
        update_post_meta(
            $post_id,
            '_hyic_partner_link',
            $_POST['hyic_partner_link']
        );
    }
}
add_action('save_post', 'hyic_partner_save_postdata');

?>