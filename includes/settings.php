<?php
/**
 * SETTINGS
 */

class ForeignRatesSettings {

	private $foreign_rates_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'foreign_rates_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'foreign_rates_settings_page_init' ) );
	}

	public function foreign_rates_settings_add_plugin_page() {
		add_menu_page(
			'Foreign Rates Settings', // page_title
			'Foreign Rates Settings', // menu_title
			'manage_options', // capability
			'foreign-rates-settings', // menu_slug
			array( $this, 'foreign_rates_settings_create_admin_page' ), // function
			'dashicons-chart-line', // icon_url
			26 // position
		);
	}

	public function foreign_rates_settings_create_admin_page() {
		$this->foreign_rates_settings_options = get_option( 'foreign_rates_settings_option_name' ); ?>

		<div class="wrap">
			<h1 class="wp-heading-inline">Foreign Rates Settings</h1>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'foreign_rates_settings_option_group' );
					do_settings_sections( 'foreign-rates-settings-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function foreign_rates_settings_page_init() {
		register_setting(
			'foreign_rates_settings_option_group', // option_group
			'foreign_rates_settings_option_name', // option_name
			array( $this, 'foreign_rates_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'foreign_rates_settings_setting_section', // id
			'Settings', // title
			array( $this, 'foreign_rates_settings_section_info' ), // callback
			'foreign-rates-settings-admin' // page
		);

		add_settings_field(
			'base', // id
			'Base currency:', // title
			array( $this, 'base_callback' ), // callback
			'foreign-rates-settings-admin', // page
			'foreign_rates_settings_setting_section' // section
		);

		add_settings_field(
			'symbols', // id
			'Convert to', // title
			array( $this, 'symbols_callback' ), // callback
			'foreign-rates-settings-admin', // page
			'foreign_rates_settings_setting_section' // section
		);


		add_settings_field(
			'display_in', // id
			'Display in:', // title
			array( $this, 'display_in_callback' ), // callback
			'foreign-rates-settings-admin', // page
			'foreign_rates_settings_setting_section' // section
		);

		add_settings_field(
			'enabled', // id
			'Enabled?', // title
			array( $this, 'enabled_callback' ), // callback
			'foreign-rates-settings-admin', // page
			'foreign_rates_settings_setting_section' // section
		);
	}

	public function foreign_rates_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['base'] ) ) {
			$sanitary_values['base'] = $input['base'];
        }
        if ( isset( $input['symbols'] ) ) {
			$sanitary_values['symbols'] = $input['symbols'];
		}
		if ( isset( $input['display_in'] ) ) {
			$sanitary_values['display_in'] = $input['display_in'];
		}

		if ( isset( $input['enabled'] ) ) {
			$sanitary_values['enabled'] = $input['enabled'];
		}

		return $sanitary_values;
	}

	public function foreign_rates_settings_section_info() {
		
	}

	public function base_callback() {
		
		?> <select name="foreign_rates_settings_option_name[base]" id="base">
			<?php $selected = (isset( $this->foreign_rates_settings_options['base'] ) && $this->foreign_rates_settings_options['base'] === 'CAD') ? 'selected' : '' ; ?>
			<option value="CAD" <?php echo $selected; ?>>CAD</option>
			<?php $selected = (isset( $this->foreign_rates_settings_options['base'] ) && $this->foreign_rates_settings_options['base'] === 'CHF') ? 'selected' : '' ; ?>
			<option value="CHF" <?php echo $selected; ?>>CHF</option>
			<?php $selected = (isset( $this->foreign_rates_settings_options['base'] ) && $this->foreign_rates_settings_options['base'] === 'EUR') ? 'selected' : '' ; ?>
			<option value="EUR" <?php echo $selected; ?>>EUR</option>
			<?php $selected = (isset( $this->foreign_rates_settings_options['base'] ) && $this->foreign_rates_settings_options['base'] === 'GBP') ? 'selected' : '' ; ?>
			<option value="GBP" <?php echo $selected; ?>>GBP</option>
			<?php $selected = (isset( $this->foreign_rates_settings_options['base'] ) && $this->foreign_rates_settings_options['base'] === 'USD') ? 'selected' : '' ; ?>
			<option value="USD" <?php echo $selected; ?>>USD</option>
			
			
		</select> <?php
	}

	public function symbols_callback() {
		$base = null;
        $check_symbols = $this->foreign_rates_settings_options['symbols'];

		$symbols = $this->foreign_rates_settings_options['symbols'];
		?>
		<p>
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
				$checked = null;
				echo '<label><input type="checkbox" class="checkbox" value="'.$key_fr.'"';
				if(in_array($key_fr, explode(',', $symbols))) { echo 'checked'; }
				if($base === $name_fr) { echo ' disabled'; }
				echo '/>'.$key_fr.'</label>';
					} ?>
			<input type="hidden" name="foreign_rates_settings_option_name[symbols]" id="symbols" value="<?php echo $symbols; ?>">
		</p>       
		<script>
        (function ($, root, undefined) {
            $(function() {
				var base = $('#base').val();
				
				$('.checkbox[value="'+base+'"]').removeAttr('checked').parent().hide();
                $('#base').change(function() {
                    $('.checkbox[value="'+$(this).val()+'"]').removeAttr('checked').parent().hide();
					$('.checkbox[value!="'+$(this).val()+'"]').removeAttr('disabled').parent().show();
					console.log($(this).val());
                    var text = "";
                    $('.checkbox:checked').each(function() {
                        text+=$(this).val()+ ',';
                    });
                    text = text.substring(0, text.length-1);
                    $('#symbols').val(text);
                });
                $('.checkbox').click(function() {
                    var text = "";
                    $('.checkbox:checked').each(function() {
                        text+=$(this).val()+ ',';
                    });
                    text = text.substring(0, text.length-1);
                    $('#symbols').val(text);
                });
            });
        })(jQuery, this);
        </script>
        <?php 

	}

	public function display_in_callback() {

		function my_cat_tree($catId, $depth){

			global $wpdb;

			$sql = $wpdb->get_results("SELECT `option_value` FROM {$wpdb->prefix}options WHERE `option_name`='foreign_rates_settings_option_name'");
			$data = unserialize($sql[0]->option_value);

			$display_in = $data['display_in'];
			
			$cat = null;
			$term_id = null;
			$depth .= '-';  
			$output ='';
			$args = 'hierarchical=1&taxonomy=category&hide_empty=0&parent=';    
			$categories = get_categories($args . $catId);
			if(count($categories)>0){
				
				foreach ($categories as $category) {
					$output .=  '<option value="'.$category->slug.'" ';
					if($display_in == $category->slug) {
						$output .= 'selected';
					}
					$output .= '>'.$depth.$category->cat_name.'</option>';
					$output .=  my_cat_tree($category->cat_ID,$depth);
				}
				
				return $output;
			}
		}
		echo '<select name="foreign_rates_settings_option_name[display_in]" id="display_in">';
		echo my_cat_tree(0,'');
		echo '</select>';

	}

	public function enabled_callback() {
		printf(
			'<input type="checkbox" name="foreign_rates_settings_option_name[enabled]" id="enabled" value="enabled" %s> <label for="enabled">Show in posts</label>',
			( isset( $this->foreign_rates_settings_options['enabled'] ) && $this->foreign_rates_settings_options['enabled'] === 'enabled' ) ? 'checked' : ''
		);
	}

}
if ( is_admin() ) {	$foreign_rates_settings = new ForeignRatesSettings(); }

?>
