<?php

function fr_show_in_post($id) {
    global $wpdb, $post;

	$sql = $wpdb->get_results("SELECT `option_value` FROM {$wpdb->prefix}options WHERE `option_name`='foreign_rates_settings_option_name'");
    $data = unserialize($sql[0]->option_value);

    $base = strtoupper($data['base']);
    $symbols = strtoupper($data['symbols']);
    $display_in = $data['display_in'];
    $enabled = $data['enabled'];

    // Get default BASE if empty

    // Check if category is 'Currency'
    $category = get_the_category( $id );
    $category_slug = $category[0]->slug;


    switch ($base) {
        case "EUR":
        $currency = "Euro";
        break;
        case "USD":
        $currency = "US Dollar";
        break;
        case "CHF":
        $currency = "Swiss Franc";
        break;
        case "GBP":
        $currency = "British Pound";
        break;
        case "CAD":
        $currency = "Canadian Dollar";
        break;
        default:
    }   
    
    // Check if 'rate' is greather then 1 and date older than one week
    $args = array(
        'p' => $id,
        'post_type' => 'post',
        'meta_key' => 'rate',
        'meta_query' => array(
                'key' => 'rate',
                'value' => 1,
                'compare' => '>'
        ),
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'date_query' => array(
            array('before' => '1 week ago')
        )
    );

    $query = new WP_Query($args);
    $query->the_post();
    if($query->post->ID) {
        $meta_ok = true;
    };
    
    // Compare meta and show if 'EUR'

    $tag_to_check = 'eur';

    $tags = get_the_tags( $id );
    
    if ( $tags ) {
        foreach ( $tags as $tag ) {
            $tag_names[] = $tag->slug;
        }
        $tag_compare = implode( ',', $tag_names );
    }

    if(in_array($tag_to_check, explode(',', $tag_compare))) {
        $tag_ok = 1;
    }

    // Display rates if everything is OK

    if($enabled && $display_in == $category_slug && $tag_ok == 1 && $meta_ok == true) {
        echo '<div class="current_rates">';
        echo '<h3><i class="fas fa-chart-line"></i> '.__( 'Current rates', 'foreign_rates' ).'</h3>';
        echo '<div class="flex">';
        if ( $base ) {
					
            $string = file_get_contents(ER_PLUGIN_DIR .'/json/data.json', true);
            $json_a = json_decode($string, true);

            if(!$json_a['rates'][$base]) { $rate = 1; } else {   $rate = $json_a['rates'][$base];  }

            echo '<div>';
            echo '<h2 data-base="'.$rate.'">1 ' . $base . '</h2>';
            echo '</div>';
            echo '<div>';
            echo ' = ';
            echo '</div>';
        }
        
        if ( $symbols ) {
            if($json_a['rates'][$base]) {
                echo '<div class="text"><h4>'.$json_a['base'].'</h4><p class="value">'.round($json_a['rates'][$base], 6).'</p></div>';
            }
            foreach($json_a['rates'] as $key => $value) {
                
                if(in_array($key, explode(',', $symbols))) {
                    if(($value/$rate) != 1) {
                        echo '<div class="text"><h4>'.$key.'</h4><p class="value">'.round($value/$rate, 6).'</p></div>';
                    }
                }
                
            }
            
        }
        echo '</div>';
        echo '</div>';
    }
}
unset($base);
unset($symbol);
unset($currency);
unset($json_a);
unset($value);
unset($rate);
?>
