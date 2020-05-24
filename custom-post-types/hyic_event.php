<?php

function hyic_setup_event_type() {
    register_post_type('hyic_event',
        array(
            'labels'      => array(
                'name'          => __('Events', 'textdomain'),
                'singular_name' => __('Event', 'textdomain'),
            ),
            'supports' => array('title', 'editor', 'excerpt'),
            'public'      => true,
            'has_archive' => true,
            'menu_icon'=>'dashicons-calendar-alt',
        )
    );
} 
add_action( 'init', 'hyic_setup_event_type' );


function hyic_add_event_dates_box()
{
    $screens = ['hyic_event'];
    foreach ($screens as $screen) {
        add_meta_box(
            'hyic_event_dates',           // Unique ID
            'Termine',  // Box title
            'hyic_event_dates_box_html',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}
add_action('add_meta_boxes', 'hyic_add_event_dates_box');
 
function hyic_event_dates_box_html($post)
{
    $eventDate = get_post_meta($post->ID, '_hyic_event_date', true);
    $registrationDeadline = get_post_meta($post->ID, '_hyic_event_registration_deadline', true);
    ?>
    <label for="hyic_event_date">Tag des Events:</label>
    <input type="date" name='hyic_event_date' value="<?php echo $eventDate;?>" /><br>
    <label for="hyic_event_deadline">Anmeldefrist:</label>
    <input type="date" name='hyic_event_deadline' value="<?php echo $registrationDeadline;?>" />
    <?php
}

function hyic_add_event_registration_box()
{
    $screens = ['hyic_event'];
    foreach ($screens as $screen) {
        add_meta_box(
            'hyic_event_registration',           // Unique ID
            'Anmeldung',  // Box title
            'hyic_event_registration_box_html',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}
add_action('add_meta_boxes', 'hyic_add_event_registration_box');
 
function hyic_event_registration_box_html($post)
{
    $registrationActive = get_post_meta($post->ID, '_hyic_event_registration_active', true);
    $registrationLink = get_post_meta($post->ID, '_hyic_event_registration_link', true);
    ?>
    <input type='checkbox' name='hyic_event_registration_active' value="true" <?php if($registrationActive=='true'){ echo 'checked';}?>>
    <label>Anmeldung möglich</label><br><br>
    <label for="hyic_event_registration_link">Link zur Registrierung (über z.B. Eventbrite):</label><br><br>
    <input type="text" name='hyic_event_registration_link' value="<?php echo $registrationLink;?>" style='width: 100%'/>
    <?php
}

function hyic_event_save_postdata($post_id)
{
    if (array_key_exists('hyic_event_date', $_POST)) {
        update_post_meta(
            $post_id,
            '_hyic_event_date',
            $_POST['hyic_event_date']
        );
    }
    if (array_key_exists('hyic_event_deadline', $_POST)) {
        update_post_meta(
            $post_id,
            '_hyic_event_registration_deadline',
            $_POST['hyic_event_deadline']
        );
    }
    update_post_meta(
        $post_id,
        '_hyic_event_registration_active',
        $_POST['hyic_event_registration_active']
    );
    if (array_key_exists('hyic_event_registration_link', $_POST)) {
        update_post_meta(
            $post_id,
            '_hyic_event_registration_link',
            $_POST['hyic_event_registration_link']
        );
    }
}
add_action('save_post', 'hyic_event_save_postdata');

?>