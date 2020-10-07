<?php
// Creating the widget 
class Foreign_Rates extends WP_Widget {
  
	// Main constructor
	public function __construct() {
		parent::__construct(
			'Foreign_Rates',
			__( 'Foreign Rates', 'foreign_rates' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}

	// The widget form (for the backend )
	public function form( $instance ) {

		// Set widget defaults
		$defaults = array(
			'title'    => '',
			'base'   => '',
			'symbols'   => '',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'foreign_rates' ); ?></label>
			<input name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
		</p>

		<?php // Dropdown ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'base' ) ); ?>"><?php _e( 'Base currency:', 'foreign_rates' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'base' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'base' ) ); ?>" class="widefat">
			<?php
			// Your options array
			$options = array(
				'CAD' => __( 'CAD', 'foreign_rates' ),
				'CHF' => __( 'CHF', 'foreign_rates' ),
				'EUR' => __( 'EUR', 'foreign_rates' ),
				'GBP' => __( 'GBP', 'foreign_rates' ),
				'USD' => __( 'USD', 'foreign_rates' ),
			);

			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $base, $key, false ) . '>'. $name . '</option>';
			} ?>
			</select>
		</p>
    	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'symbols' ) ); ?>"><?php _e( 'Convert to:', 'foreign_rates' ); ?></label><br />
      	<?php
			// Your options array
			$options_fr = array(
				'CAD' => __( 'CAD', 'foreign_rates' ),
				'CHF' => __( 'CHF', 'foreign_rates' ),
				'EUR' => __( 'EUR', 'foreign_rates' ),
				'GBP' => __( 'GBP', 'foreign_rates' ),
				'USD' => __( 'USD', 'foreign_rates' ),
			);

			// Loop through options and add each one to the select dropdown
			foreach ( $options_fr as $key_fr => $name_fr ) {
				echo '<label><input type="checkbox" class="checkbox" value="'.$key_fr.'"';
				if(in_array($key_fr, explode(',', $symbols))) { echo 'checked'; }
				if($base === $name_fr) { echo ' disabled'; }
				echo '/>'.$key_fr.'</label>';
			} 
		?>
      <input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'symbols' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'symbols' ) ); ?>" value="<?php echo $symbols; ?>">
      </p>
	  <?php 

	  global $pagenow;

      if( $pagenow === 'widgets.php' || basename($_SERVER['HTTP_REFERER']) ==='widgets.php') { ?>
      <script>
        (function ($, root, undefined) {
            $(function() {
              	var base = $('#<?php echo esc_attr( $this->get_field_id( 'base' ) ); ?>').val();
				$('.checkbox[value="'+base+'"]:disabled').parent().hide();
                $('#<?php echo esc_attr( $this->get_field_id( 'base' ) ); ?>').change(function() {
					$('.checkbox[value="'+$(this).val()+'"]').removeAttr('checked').parent().hide();
					$('.checkbox[value!="'+$(this).val()+'"]').removeAttr('disabled').parent().show();
                    var text = "";
                    $('.checkbox:checked').each(function() {
                        text+=$(this).val()+ ',';
                    });
                    text = text.substring(0, text.length-1);
                    $('#<?php echo esc_attr( $this->get_field_id( 'symbols' ) ); ?>').val(text);
                });
                $('.checkbox').click(function() {
                    var text = "";
                    $('.checkbox:checked').each(function() {
                        text+=$(this).val()+ ',';
                    });
                    text = text.substring(0, text.length-1);
                    $('#<?php echo esc_attr( $this->get_field_id( 'symbols' ) ); ?>').val(text);
                });
            });
        })(jQuery, this);
        </script>
        <?php } ?>
	<?php }

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['base']   = isset( $new_instance['base'] ) ? wp_strip_all_tags( $new_instance['base'] ) : '';
		$instance['symbols']   = isset( $new_instance['symbols'] ) ? wp_strip_all_tags( $new_instance['symbols'] ) : '';
		return $instance;
	}

	// Display the widget
	public function widget( $args, $instance ) {

		extract( $args );

		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$base   = isset( $instance['base'] ) ? $instance['base'] : '';
		$symbols   = isset( $instance['symbols'] ) ? $instance['symbols'] : '';

		// WordPress core before_widget hook (always include )
		echo $before_widget;

		// Display the widget
		echo '<div class="widget-text wp_widget_plugin_box fr_widget">'; 

			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			echo '<div class="row">';
			if ( $base ) {

				$string = file_get_contents(ER_PLUGIN_DIR .'/json/data.json', true);
				$json_a = json_decode($string, true);
	
				if(!$json_a['rates'][$base]) { $rate = 1; } else {   $rate = $json_a['rates'][$base];  }
	
				echo '<div class="col col-4 base">';
					echo '<img src="'. plugins_url() .'/' . ER_PLUGIN_NAME .'/assets/img/flags/'.strtolower($base).'.svg" alt="" />';
					echo '<h2 data-base="'.$rate.'">1 ' . $base . '</h2>';
					echo '</div>';
			}
			
			if ( $symbols ) {
				echo '<div class="col col-8">';
					echo '<div class="row rates-list">';
					if($json_a['rates'][$base]) {
						echo '<div class="col col-6"><div class="text"><h4>'.$json_a['base'].'</h4><p class="value">'.round($json_a['rates'][$base], 6).'</p></div></div>';
					}
					foreach($json_a['rates'] as $key => $value) {
						if(in_array($key, explode(',', $symbols))) {
							if(($value/$rate) != 1) {
								echo '<div class="col col-6"><div class="text"><h4>'.$key.'</h4><p class="value">'.round($value/$rate, 6).'</p></div></div>';
							}
						}
						
					}
					echo '</div>';
					echo '</div>';
				
			}
				echo '</div>';
			echo '</div>';
		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}
}

unset($base);
unset($symbol);
unset($currency);
unset($json_a);
unset($value);
unset($rate);

// Register the widget
function foreign_rates_widget() {
	register_widget( 'Foreign_Rates' );
}
add_action( 'widgets_init', 'foreign_rates_widget' );

