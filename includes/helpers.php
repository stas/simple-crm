<?php

/**
 * template_render( $name, $vars = null, $echo = true )
 *
 * Helper to load and render templates easily
 * @param String $name, the name of the template
 * @param Mixed $vars, some variables you want to pass to the template
 * @param Boolean $echo, to echo the results or return as data
 * @return String $data, the resulted data if $echo is `false`
 */
function template_render( $name, $vars = null, $echo = true ) {
    ob_start();
    if( !empty( $vars ) )
        extract( $vars );
    
    if( !isset( $path ) )
        $path = dirname( __FILE__ ) . '/templates/';
    
    include $path . $name . '.php';
    
    $data = ob_get_clean();
    
    if( $echo )
        echo $data;
    else
        return $data;
}

?>
