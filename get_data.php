<?php 

 // Query the server 
 function callback_function_name() {

    global $wpdb;

    $sql = $wpdb->get_results("SELECT `option_value` FROM `er_options` WHERE `option_name`='foreign_rates_settings_option_name'");
    $data = unserialize($sql[0]->option_value);
    
    $base = strtoupper($data['base']);
    $symbols = strtoupper($data['symbols']);

    if(!$base) { $base = 'EUR'; }
    
    $url = 'https://api.exchangeratesapi.io/latest?base='.$base.'&symbols='.$symbols;
    $arguments = array(
        'method' => 'GET'
    );

    $response = wp_remote_get ( $url, $arguments );
    
    if( 200 == wp_remote_retrieve_response_code( $response ) ) {
        $file_link = ER_PLUGIN_DIR . '/json/data.json';

        $message = wp_remote_retrieve_body( $response );
        write_to_file( $message, $file_link );
    }

    if( 200 !== wp_remote_retrieve_response_code( $response ) ) {
        $file_link = ER_PLUGIN_DIR . '/json/error-log.txt';

        $error_message = $response->get_error_message;
        $message = date( 'd m Y g:i:a' ) . ' ' . wp_remote_retrieve_response_code( $response ) . ' ' . $error_message;
        write_to_file( $message, $file_link );
    }
 }

 function write_to_file( $message, $file_link ) {
     if ( file_exists ( $file_link ) ) {
         $filling = fopen ( $file_link, 'w' );
         fwrite( $filling, $message . "\n" );
     }
     else {
        $filling = fopen ( $file_link, 'w' );
        fwrite( $filling, $message . "\n" );
     }

     fclose( $filling );
 }

 callback_function_name();
?>