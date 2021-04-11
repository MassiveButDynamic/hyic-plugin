<?php

function mytheme_customize_register( $wp_customize ) {
    $wp_customize->add_setting( 'og_image_link' , array(
        'default'   => '',
        'transport' => 'refresh',
    ) );

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'og_image_link', array(
        'label'      => __( 'Link-Vorschaubild', 'mytheme' ),
        'description' => 'Das Bild, was in sozialen Netzwerken oder Messengern als Vorschau angezeigt wird, wenn du den Link zu dieser Website verschickst/teilst.',
        'section'    => 'title_tagline',
        'settings'   => 'og_image_link',
        'priority' => 80,
    ) ) );
}
add_action( 'customize_register', 'mytheme_customize_register' );


?>