<?php

require_once 'helpers.php';

/**
 * Class extends user profile fields
 */
class SCRM {
    /**
     * Static constructor
     */
    function init() {
        add_action( 'admin_head', array( __CLASS__, 'check_force_redirect' ) );
        add_action( 'admin_menu', array( __CLASS__, 'menus' ) );
        add_action( 'show_user_profile', array( __CLASS__, 'profile_fields' ) );
        add_action( 'personal_options_update', array( __CLASS__, 'profile_fields_update' ) );
        // This one for admins to be able to update
        add_action( 'edit_user_profile', array( __CLASS__, 'profile_fields' ) );
        add_action( 'edit_user_profile_update', array( __CLASS__, 'profile_fields_update' ) );
    }
    
    /**
     * Adds menu entries to `wp-admin`
     */
    function menus() {
        add_options_page(
            __( 'CRM', 'scrm' ),
            __( 'CRM', 'scrm' ),
            'administrator',
            'scrm',
            array( __CLASS__, "screen" )
        );
    }
    
    /**
     * Menu screen handler in `wp-admin`
     */
    function screen() {
        $flash = null;
        $field = array();
        $field_name = null;
        
        do_action( 'scrm_options_screen_updated' );
        
        // Delete field
        if( isset( $_GET['_nonce'] ) && wp_verify_nonce( $_GET['_nonce'], 'scrm_delete' ) ) {
            if( isset( $_GET['del'] ) && !empty( $_GET['del'] ) )
                if( self::delete_field( $_GET['del'] ) )
                    $flash = sprintf( __( 'Field: %s was deleted.', 'scrm' ), $_GET['del'] );
                else
                    $flash = sprintf( __( 'Field: %s was not deleted.', 'scrm' ), $_GET['del'] );
        }
        // Edit field
        if( isset( $_GET['_nonce'] ) && wp_verify_nonce( $_GET['_nonce'], 'scrm_edit' ) )
            if( isset( $_GET['edit'] ) && !empty( $_GET['edit'] ) )
                $field_name = $_GET['edit'];
        
        // Add field
        if( isset( $_POST['scrm_nonce'] ) && wp_verify_nonce( $_POST['scrm_nonce'], 'scrm' ) ) {
            if( isset( $_POST['field_title'] ) && !empty( $_POST['field_title'] ) )
                $field['title'] = sanitize_text_field( $_POST['field_title'] );
            
            if( isset( $_POST['field_name'] ) && !empty( $_POST['field_name'] ) )
                $field['name'] = sanitize_key( $_POST['field_name'] );
            
            if( isset( $_POST['field_type'] ) && !empty( $_POST['field_type'] ) )
                $field['type'] = sanitize_key( $_POST['field_type'] );
            
            if( count( $field ) == 3 )
                if( self::add_field( $field ) )
                    $flash = __( 'New fields was saved.', 'scrm' );
                else
                    $flash = __( 'New fields was not saved.', 'scrm' );
        }
        
        // Update user profiles option
        if( isset( $_POST['scrm_force_nonce'] ) && wp_verify_nonce( $_POST['scrm_force_nonce'], 'scrm' ) ) {
            if( isset( $_POST['force_redirect'] ) && sanitize_key( $_POST['force_redirect'] ) == 'on' )
                update_option( 'scrm_force_redirect', 1 );
            else
                update_option( 'scrm_force_redirect', 0 );
            
        }
        
        $vars['scrm_permalink'] = menu_page_url( 'scrm', false );
        $vars['edit_permalink'] = add_query_arg( '_nonce', wp_create_nonce('scrm_edit'), $vars['scrm_permalink'] );
        $vars['delete_permalink'] = add_query_arg( '_nonce', wp_create_nonce('scrm_delete'), $vars['scrm_permalink'] );
        $vars['fields'] = self::get_fields();
        $vars['field'] = self::get_field( $field_name );
        $vars['flash'] = apply_filters( 'scrm_screen_flash', $flash );
        $vars['force_redirect'] = get_option( 'scrm_force_redirect' );
        template_render( 'options', $vars );
    }
    
    /**
     * Checks for current user profile info status and redirects if no fields were updated
     */
    function check_force_redirect() {
        $option = false;
        
        if( isset( $_GET['please'] ) && $_GET['please'] == 'update' )
            add_action( 'admin_notices', array( __CLASS__, 'admin_notice_for_user' ) );
        
        // Ignore admin
        if( is_super_admin() || current_user_can('manage_options') )
            return;
        else
            $option = get_option( 'scrm_force_redirect' );
        
        $user = wp_get_current_user();
        $current_url = get_site_url() . $_SERVER["PHP_SELF"];
        $user_url = apply_filters( 'scrm_force_redirect_url', get_edit_profile_url( $user->ID ) );
        if( !self::check_profile_fields( $user->ID ) )
            if( $option && $current_url != $user_url )
                wp_redirect( add_query_arg( array( 'please' => 'update' ), $user_url ) );
    }
    
    /**
     * Literally checks for each current user field status
     */
    function check_profile_fields( $user_id ) {
        $fields = self::get_fields();
        foreach ( $fields as $f )
            if( !get_user_meta( $user_id, $f['name'], true ) ) 
                return false;
        
        return true;
    }
    
    /**
     * Adds a notification
     */
    function admin_notice_for_user() {
        $vars['flash'][] = __( 'Please update your profile before accesing the website.','scrm' );
        $vars['flash'][] = __( 'The information below has to be updated.','scrm' );
        template_render( 'admin_notice', $vars, true );
    }
    
    /**
     * Appends profile fields to profile page
     */
    function profile_fields( $profile ) {
        $fields = self::get_fields();
        $fields_data = array();
        foreach( $fields as $f )
            $fields_data[$f['name']] = get_user_meta( $profile->ID, $f['name'], true );
        
        $vars['fields'] = $fields;
        $vars['fields_data'] = $fields_data;
        template_render( 'profile', $vars );
    }
    
    /**
     * Saves profile fields posted data
     */
    function profile_fields_update( $user_id ) {
        if( isset( $_POST['scrm_nonce'] ) && wp_verify_nonce( $_POST['scrm_nonce'], 'scrm' ) ) {
            if( isset( $_POST['scrm'] ) && !empty( $_POST['scrm'] ) )
                foreach( $_POST['scrm'] as $field_name => $field_data )
                    update_user_meta( $user_id, sanitize_key( $field_name ), sanitize_text_field( $field_data ) );
        }
    }
    
    /**
     * Updates SCRM fields options with new $field
     */
    function add_field( $field ) {
        $is_duplicate = null;
        $wrong_fields = array(
            'role',
            'capabilities',
            'user_level',
            'usersettings',
            'aim',
            'yim',
            'nickname',
            'first_name',
            'last_name',
            'jabber',
            'description',
            'user_url'
        );
        
        foreach( $wrong_fields as $wrong_field )
            if( preg_match( "/".$field['name']."/", $wrong_field ) )
                return false;
        
        $fields = get_option( 'scrm_fields' );
        if( !$fields )
            $fields = array( $field );
        else {
            $fields = maybe_unserialize( $fields );
            if( is_array( $fields ) ) {
                for ( $fid = 0; $fid <= count( $fields ); $fid++ )
                    if( isset( $fields[$fid] ) && $fields[$fid]['name'] == $field['name'] ) {
                        $is_duplicate = $fid;
                        break;
                    }
                if( $is_duplicate != null )
                    $fields[$is_duplicate] = $field;
                else
                    $fields[] = $field;
            }
            else
                return false;
        }
        $fields = array_values( $fields );
        $fields = maybe_serialize( $fields );
        return update_option( 'scrm_fields', $fields );
    }
    
    /**
     * Deltes a SCRM field with $name
     */
    function delete_field( $name ) {
        $name = sanitize_key( $name );
        $fields = self::get_fields();
        if( is_array( $fields ) )
            for( $fid = 0; $fid <= count( $fields ); $fid++ )
                if( isset( $fields[$fid] ) && $fields[$fid]['name'] == $name )
                    unset( $fields[$fid] );
        
        $fields = array_values( $fields );
        $fields = maybe_serialize( $fields );
        return update_option( 'scrm_fields', $fields );
    }
    
    /**
     * Returns SCRM fields
     */
    function get_fields() {
        $fields = get_option( 'scrm_fields' );
        return maybe_unserialize( $fields );
    }
    
    /**
     * Returns SCRM field with $name
     */
    function get_field( $name ) {
        $name = sanitize_key( $name );
        $fields = self::get_fields();
        if( is_array( $fields ) )
            foreach( $fields as $f )
                if( $f['name'] == $name ) {
                    return $f;
                    break;
                }
        return array( 'title' => '', 'name' => '', 'type' => '' );
    }
}

?>
