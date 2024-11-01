<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://aliayubi.com
 * @since             1.2.1
 * @package           Gdpr_ultimate
 *
 * @wordpress-plugin
 * Plugin Name:       WP GDPR Ultimate
 * Plugin URI:        https://aliayubi.com/wp-gdpr-ultimate/
 * Description:       With using this plugin you can easly make your website GDPR compliance.
 * Version:           1.2.1
 * Author:            Ali Ayubi
 * Author URI:        https://aliayubi.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gdpr_ultimate
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GDPRU_VERSION', '1.2.1' );
define('GDPRU_DIR', __FILE__);
define('GDPRU_CDIR', plugin_dir_path(GDPRU_DIR));
define('GDPRU_XBOX', GDPRU_CDIR . 'admin/xbox');


//Including XBOX framework
include(GDPRU_XBOX. '/xbox.php');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gdpr_ultimate-activator.php
 */
function activate_gdpr_ultimate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gdpr_ultimate-activator.php';
	Gdpr_ultimate_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gdpr_ultimate-deactivator.php
 */
function deactivate_gdpr_ultimate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gdpr_ultimate-deactivator.php';
	Gdpr_ultimate_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gdpr_ultimate' );
register_deactivation_hook( __FILE__, 'deactivate_gdpr_ultimate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gdpr_ultimate.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gdpr_ultimate() {

	$plugin = new Gdpr_ultimate();
	$plugin->run();

}
/**
 * Loading Xbox Class
 */
require_once(GDPRU_XBOX. '/loader.php');
$loader = new XboxLoader120( '1.2.0', 980 );
$loader->load_xbox();
$xbox = Xbox::get( 'wp-gdpr-ultimate' ); // Get Option page ID
$usingform = $xbox->get_field_value('using-form');

// If showing form is enabled in option page then run function
if ($usingform == "on") {
	/**
	 * Function for inserting GDPR Checkbox to forms
	 */
	function gdpruAddCheckbox($xbox){
		global $xbox;
		// Get option fields value
		$checkbox_label = $xbox->get_field_value('checkbox-label');
		$excludeformbyidget = $xbox->get_field_value('excludeformbyid');
		$excludeformbyclassget = $xbox->get_field_value('excludeformbyclass');
		$selected_privacypage = $xbox->get_field_value('select-privacy-page');
		$privacypage = esc_url( get_permalink( get_page_by_title( $selected_privacypage ) ) ); 
		$linkpage_checkbox = $xbox->get_field_value('linkpage-checkbox');
		$linkpage_notification = $xbox->get_field_value('linkpage-notifacation');
		$linkpage_text = $xbox->get_field_value('linkpage-text');
		// Exclude form by ID and Classes that has been defined in option page
		$excludeformbyidexplode = explode(',', $excludeformbyidget);
		// Some common IDs has been excluded by default
		array_unshift($excludeformbyidexplode, "adminbarsearch");
		$excludeformbyidEmplode = join(', #', $excludeformbyidexplode);
		$excludeformbyclassexplode = explode(',', $excludeformbyclassget);
		// Some common classes has been excluded by default
		array_unshift($excludeformbyclassexplode, " ,.cart,.woocommerce-cart-form,.login,.lost_reset_password,.addtocartform");
		$excludeformbyclassEmplode = join(', .', $excludeformbyclassexplode);
		?>
		<!-- Below JS finding used forms in website and include checkbox before submit btn -->
		<script type="text/javascript">
		var $ = jQuery.noConflict();
		$( document ).ready(function() {
		    var form =  $('body').find('form').not('<?php echo $excludeformbyidEmplode.$excludeformbyclassEmplode; ?>');
		    $(form).each(function() {
					var type = $(this).attr('method');
					if ("<?php echo $linkpage_checkbox; ?>" == "on") {
					var link_checkbox = '<a href="<?php echo $privacypage; ?>"><?php echo $linkpage_text; ?></a>';
					}
					else {
					var link_checkbox = ' ';
					}
					// console.log(type);
					if (type != "get") {
								      if($(this).find('input:text')){
		     	$(this).find('button').before('<div class="wrap gdprcontainer"> <ul style="display:none;" class="error"></ul><ul> <li> <input name="gdrp" id="checkbox1" value="1" type="checkbox" required> <label for="checkbox1"><?php echo $checkbox_label; ?> </lable> </li>'+ link_checkbox +' </ul></div>');
							$(this).find('input:submit').before('<div class="wrap gdprcontainer"> <ul style="display:none;" class="error"></ul><ul> <li> <input name="gdrp" id="checkbox1" value="1" type="checkbox" required> <label for="checkbox1"><?php echo $checkbox_label; ?> </lable>  </li>'+ link_checkbox +' </ul></div>');
					}
						}
		    });
		});
		</script>
		<?php
	}
	add_action('wp_head', 'gdpruAddCheckbox');
}

// IF cookies has been not set then run function
if (!isset($_COOKIE["gdpr_notice"])) {
	/**
    * Function for Inserting Notification bar
	 */
function gdpruInsertNotification($xbox){
	global $xbox;
	// Get option fields value
	$usingnotification = $xbox->get_field_value('usenotification');
	$title = $xbox->get_field_value('notificationtitle');
	$text = $xbox->get_field_value('notificationtext');
	$selected_privacypage = $xbox->get_field_value('select-privacy-page');
	$privacypage = esc_url( get_permalink( get_page_by_title( $selected_privacypage ) ) ); 
	$linkpage_text = $xbox->get_field_value('linkpage-text');
	$linkpage_notification = $xbox->get_field_value('linkpage-notifacation');
// If Notification bar has been to on in option page then run JS code
	if ($usingnotification == "on") {
?>
<script type="text/javascript">
$(function(){
	if ("<?php echo $linkpage_notification; ?>" == "on") {
		var link_notify = '<a href="<?php echo $privacypage; ?>"><?php echo $linkpage_text; ?></a>';
	}
	else {
		var link_notify = ' ';
	}
	new PNotify({
	    title: '<?php echo $title; ?>',
	    text: '<?php echo $text; ?> ' + link_notify + '',
			type: 'info',
	    hide: false,
			icon: false,
			addclass: "stack-bottomleft",
			stack: {"dir1": "right", "dir2": "up", "push": "top"}
	});
	// Set Cookie after Close Button has been clicked
	$('span.brighttheme-icon-closer').click(function() {
  document.cookie = "gdpr_notice=1; expires=<?php echo time()+3600; ?>; path=/";
});
});
</script>
<?php
	}
	}
	add_action('wp_head', 'gdpruInsertNotification');
	}


// If cookies has been set then Remove Notification bar
if (isset($_COOKIE["gdpr_notice"])) {
	function gdpruHideNotification(){
	?>
<script type="text/javascript">
$(document).ready(function (){
				$('.ui-pnotify').remove();
});
</script>
	<?php
	}
	add_action('wp_head', 'gdpruHideNotification');
}

run_gdpr_ultimate();
