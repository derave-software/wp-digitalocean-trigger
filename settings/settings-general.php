<?php
/**
 * WordPress Settings Framework
 *
 * @author  Gilbert Pellegrom, James Kemp
 * @link    https://github.com/gilbitron/WordPress-Settings-Framework
 * @license MIT
 */

add_filter( 'wpsf_register_settings_wp_do', 'wpsf_tabless_settings' );


function wpsf_tabless_settings( $wpsf_settings ) {
    $wpsf_settings[] = array(
        'section_id'          => 'general',
        'section_title'       => 'General Settings',
        'section_description' => 'Provide information from DigitalOcean',
        'section_order'       => 5,
        'fields'              => array(
            array(
                'id'          => 'DO_APP_ID',
                'title'       => 'DO App ID',
                'desc'        => 'DigitalOcean App ID',
                'placeholder' => '',
                'type'        => 'text',
                'default'     => '',
            ),
            array(
                'id'          => 'DO_TOKEN',
                'title'       => 'DO Token',
                'desc'        => 'DigitalOcean Token',
                'placeholder' => '',
                'type'        => 'text',
                'default'     => '',
            ),
            array(
                'id'      => 'Hooks',
                'title'   => 'Run build on hooks:',
                'desc'    => 'Select the hooks for build trigger ',
                'type'    => 'checkboxes',
                'default' => array(
                    'red',
                    'blue',
                ),
                'choices' => array(
                    'save_post'   => 'Save post (save_post)',
                    'user_register' => 'User Register (user_register)',
                    'profile_update'  => 'Profile Update (profile_update)',
                    'deleted_user'  => 'Delete user (deleted_user)',
                ),
            ),
        ),
    );

    return $wpsf_settings;
}
