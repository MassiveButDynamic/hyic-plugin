<?php
/**
 * Plugin Name: HYIC-Plugin
 */

//include_once(dirname(__FILE__).'/custom-post-types/hyic_project.php');
include_once(dirname(__FILE__).'/custom-post-types/hyic_event.php');
//include_once(dirname(__FILE__).'/custom-post-types/hyic_partner.php');
 

function hyic_setup_post_types() {
    //hyic_setup_project_type();
    hyic_setup_event_type();
    //hyic_setup_partner_type();
}

function hyic_unregister_post_types() {
    //unregister_post_type('hyic_project');
    unregister_post_type('hyic_event');
    //unregister_post_type('hyic_partner');
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



//Init Gutenberg-Blocks
function create_block_hyic_blocks_init() {
    $dir = __DIR__;
 
    $script_asset_path = "$dir/blocks/build/index.asset.php";
    if ( ! file_exists( $script_asset_path ) ) {
        throw new Error(
            'You need to run `npm start` or `npm run build` for the "create-block/gutenpride" block first.'
        );
    }
    $index_js     = 'blocks/build/index.js';
    $script_asset = require( $script_asset_path );
    wp_register_script(
        'create-block-gutenpride-block-editor',
        plugins_url( $index_js, __FILE__ ),
        $script_asset['dependencies'],
        $script_asset['version']
    );
    wp_set_script_translations( 'create-block-gutenpride-block-editor', 'gutenpride' );
 
    /*$editor_css = 'editor.css';
    wp_register_style(
        'create-block-gutenpride-block-editor',
        plugins_url( $editor_css, __FILE__ ),
        array(),
        filemtime( "$dir/$editor_css" )
    );
 
    $style_css = 'style.css';
    wp_register_style(
        'create-block-gutenpride-block',
        plugins_url( $style_css, __FILE__ ),
        array(),
        filemtime( "$dir/$style_css" )
    );
    */
 
    register_block_type( 'hyic/events-carousel', array(
        'apiVersion' => 2,
        'editor_script' => 'create-block-gutenpride-block-editor',
        'render_callback' => 'gutenberg_examples_dynamic_render_callback'
        //'editor_style'  => 'create-block-gutenpride-block-editor',
        //'style'         => 'create-block-gutenpride-block',
    ) );
}
add_action( 'init', 'create_block_hyic_blocks_init' );

function gutenberg_examples_dynamic_render_callback( $block_attributes, $content ) {
    // $recent_posts = wp_get_recent_posts( array(
    //     'numberposts' => 3,
    //     'post_status' => 'publish',
    //     'post_type' => 'hyic_event'
    // ) );
    $args = array(
        'post_type'      => 'hyic_event',
        'posts_per_page' => -1
    );
    $loop = new WP_Query($args);
    
    function order_events_by_startdate($a, $b) {
        $aStart = new DateTime(get_post_custom_values('_hyic_event_start_date', $a->ID)[0]);
        $bStart = new DateTime(get_post_custom_values('_hyic_event_start_date', $b->ID)[0]);
    
        return ($aStart>$bStart) ? -1 : 1;
    }
    
    usort($loop->posts, 'order_events_by_startdate');
    $recent_posts = array_slice($loop->posts, 0, 3);

    if ( count( $recent_posts ) === 0 ) {
        return 'No posts';
    }
    $result = '<div class="hyic-event-carousel-wrapper">';
    foreach($recent_posts as $post) {
        $today = new DateTime('today');

        $registrationDeadline = date_create(get_post_meta( $post->ID, '_hyic_event_registration_deadline', true ));
        
        $isAllDay = get_post_meta( $post->ID, '_hyic_event_all_day', true )=='true';
        $startDateString = get_post_meta( $post->ID, '_hyic_event_start_date', true );
        $startTimeString = get_post_meta( $post->ID, '_hyic_event_start_time', true );
        $endDateString = get_post_meta( $post->ID, '_hyic_event_end_date', true );
        $endTimeString = get_post_meta( $post->ID, '_hyic_event_end_time', true );

        $startDate = date_create($startDateString.' '.$startTimeString);
        $endDate = date_create($endDateString.' '.$endTimeString);

        $dateString = '';

        if($isAllDay) {
            if($startDateString == $endDateString) {
                $dateString = date_format($startDate, 'd.m.Y');
            } else {
                $dateString = date_format($startDate, 'd.m.').' - '.date_format($endDate, 'd.m.Y');
            }
        } else {
            if($startDateString == $endDateString) {
                $dateString = date_format($startDate, 'd.m.Y').', '.date_format($startDate, 'H:i').' bis '.date_format($endDate, 'H:i').' Uhr';
            } else {
                $dateString = date_format($startDate, 'd.m.Y H:i').' Uhr - '.date_format($endDate, 'd.m.Y H:i').' Uhr';
            }
        }

        $result .= sprintf(
            "<div class='hyic-event-card'>
                <div class='hyic-event-card-image-wrapper'>
                    <img src='%s' alt='Vorschaubild des Events %s'></img>
                </div>
                <div class='hyic-event-card-text-wrapper'>
                    <span class='hyic-event-card-title'> %s </span>
                    <span class='hyic-event-card-time'> %s </span>
                    <span class='hyic-event-card-deadline'><span>Anmeldung bis:</span><br><span class='date'>%s</span> </span>
                </div>
                <a class='hyic-event-card-button%s' href=' %s '>
                    <span>%s</span>
                </a>
            </div>
            ",
            wp_get_attachment_image_url( get_post_thumbnail_id( $post->ID ), 'post-thumbnail' ),
            esc_html( get_the_title( $post->ID ) ),
            esc_html( get_the_title( $post->ID ) ),
            $dateString,
            date_format($registrationDeadline, 'd.m.Y'),
            ($today > $registrationDeadline) ? ' more-info' : '',
            esc_url( get_permalink( $post->ID ) ),
            ($today > $registrationDeadline) ? 'Mehr erfahren' : 'Jetzt anmelden'
        );
    }
    $result .= '<div class="show-all-hyic-events"><a class="button" href="events">Alle Events ansehen</a></div></div>';

    return $result;
}
?>