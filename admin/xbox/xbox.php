<?php
/**
 * Plugin Name: Xbox Framework Lite
 * Plugin URI: http://xboxframework.com
 * Description: Xbox is a powerful framework to create beautiful, professional and flexibles Meta boxes and Admin pages. Building meta boxes and admin pages has never been easier!
 * Version: 1.2.0
 * Author: MaxLopez
 * Author URI: https://codecanyon.net/user/maxlopez
 * Text Domain: xbox
 * Domain Path: /languages/
 */

/*
|---------------------------------------------------------------------------------------------------
| Xbox Framework
|---------------------------------------------------------------------------------------------------
*/

if( ! class_exists( 'XboxLoader120', false ) ) {
	include dirname( __FILE__ ) . '/loader.php';
	$loader = new XboxLoader120( '1.2.0', 980 );
	$loader->init();
}


/*
|---------------------------------------------------------------------------------------------------
| Usage example | These files are just for the example. Comment or remove these lines if you don't need it.
|---------------------------------------------------------------------------------------------------
*/
if( function_exists('my_theme_options') || function_exists('my_simple_metabox') ){
	return;
}

if( ! defined( 'XBOX_HIDE_DEMO' ) || ( defined( 'XBOX_HIDE_DEMO' ) && ! XBOX_HIDE_DEMO ) ){
define( 'XBOX_HIDE_DEMO', true );
}

add_action( 'xbox_init', 'gdprduAdminPage');
function gdprduAdminPage(){
	$options = array(
		'id' => 'wp-gdpr-ultimate',//It will be used as "option_name" key to save the data in the wp_options table
		'title' => 'WP GDPR Ultimate',
		'menu_title' => 'WP GDPR Ultimate',
		'icon' => false,
		'skin' => 'teal',// Skins: blue, lightblue, green, teal, pink, purple, bluepurple, yellow, orange'.
		'layout' => 'wide',//Layouts: wide, boxed
		'position' => 60,
		'parent' => false,//The slug name for the parent menu (or the file name of a standard WordPress admin page).
		'capability' => 'manage_options',//https://codex.wordpress.org/Roles_and_Capabilities
		'header' => array(
			'icon' => false,
			'desc' => 'General settings for GDPR Ulitimate',
		),
		'saved_message' => __( 'Settings updated', 'xbox' ),
		'reset_message' => __( 'Settings reset', 'xbox' ),
		'form_options' => array(
			'id' => 'id-form-tag',
		  'action' => '',
		  'method' => 'post',
		  'save_button_text' => __('Save Changes', 'xbox'),
		  'save_button_class' => '',
		  'reset_button_text' => __('Reset to Defaults', 'xbox'),
		  'reset_button_class' => '',
		)
	);

	$xbox = xbox_new_admin_page( $options );

	$xbox->add_main_tab( array(
		'id' => 'main-tab',
		'items' => array(
			'forms' => '<i class="xbox-icon xbox-icon-gear"></i>Forms and Checkboxes',
			'notification' => '<i class="xbox-icon xbox-icon-info"></i>GDPR Notification',
			'privacy' => '<i class="xbox-icon xbox-icon-file-text"></i>GDPR & Privacy Policies',
		),
	));
	$xbox->open_tab_item('forms');
	$xbox->add_field(array(
		'id' => 'using-form',
		'name' => __( 'Are you using any form?', 'gdpr-integration' ),
		'desc' => __( 'According to GDPR guidelines, you as business/site owner have to inform visitors/customers about obtaining their data via ANY FORM and must get acceptance from visitors/customers.', 'gdpr-integration' ),
		'type' => 'switcher',
		'default' => 'off',
	));
	$xbox->add_field(array(
		'id' => 'checkbox-label',
		'name' => __( 'Checkbox Label', 'gdpr-integration' ),
		'desc_title' => __( 'Please write a text for checkbox Label', 'gdpr-integration' ),
		'desc' => __( 'This plugin will automaticly add a checkbox in any form that suspicious for GDPR compliance to get Customers/Visitors Confirmation regarding saving their data or information!', 'gdpr-integration' ),
		'type' => 'text',
		'grid' => '6-of-6',
		'default' => __('By checking this checkbox you agree with the storage and handling of your data by this website.', 'gdpr-integration' ),
	));

	$xbox->add_field(array(
		'id' => 'excludeformbyid',
		'name' => __( 'Exclude form by ID', 'gdpr-integration' ),
		'desc_title' => __( 'Please write ID of "Form" tag and press Enter', 'gdpr-integration' ),
		'desc' => __( 'Important: This plugin using JavaScript to detect forms automatically for saving time for website owners. If accidentally the GDPR checkbox has been added to a unnecessary form. Please use this option to exclude/remove it!', 'gdpr-integration' ),
		'type' => 'text',
		'grid' => '6-of-6',
		'default' => 'search,adminbarsearch',
		'attributes' =>  array('data-role' => 'tagsinput')
	));

	$xbox->add_field(array(
		'id' => 'excludeformbyclass',
		'name' => __( 'Exclude form by Class', 'gdpr-integration' ),
		'desc_title' => __( 'Please write Class of "Form" tag and press Enter', 'gdpr-integration' ),
		'desc' => __( 'Important: This plugin using JavaScript to detect forms automatically for saving time for website owners. If accidentally the GDPR checkbox has been added to a unnecessary form. Please use this option to exclude/remove it!', 'gdpr-integration' ),
		'type' => 'text',
		'grid' => '6-of-6',
		'default' => 'searchbar',
		'attributes' =>  array('data-role' => 'tagsinput')
	));

	$xbox->close_tab_item('forms');

	$xbox->open_tab_item('notification');
	$xbox->add_field(array(
		'id' => 'usenotification',
		'name' => __( 'Show GDPR Notification?', 'gdpr-integration' ),
		'desc' => __( 'You can use this option to show a notification bar in front-end and inform website Visitors/Customers about GDPR compliance and policy.', 'gdpr-integration' ),
		'type' => 'switcher',
		'default' => 'off',
	));
	$xbox->add_field(array(
	'id' => 'notificationtitle',
	'name' => __( 'Notification Title', 'gdpr-integration' ),
	'type' => 'text',
	'default' => 'General Data Protection Regulation (GDPR) Notice',
));
$xbox->add_field(array(
'id' => 'notificationtext',
'name' => __( 'Notification Text', 'gdpr-integration' ),
'type' => 'text',
'default' => 'We take your privacy seriously and will only use your personal information to administer your account and to provide the products and services you have requested from us.',
));
	$xbox->close_tab_item('notification');

	$xbox->open_tab_item('privacy');

	$pages = get_pages();
		$Pageoption = array("select");
		$Pageoption["- select -"] = "select" ;
foreach ( $pages as $page ) {
		$Pageoption[$page->post_title] = $page->post_title ;
}

	$xbox->add_field( array(
		'id' => 'select-privacy-page',
		'name' => __( 'Select "GDPR and Privacy Policies" Page', 'textdomain' ),
		'type' => 'image_selector',
		'default' => 'select',
				'items' => $Pageoption,
	));

		$xbox->add_field(array(
		'id' => 'linkpage-checkbox',
		'name' => __( 'Add "GDPR and Privacy Policies" page link to GDPR Checkbox?', 'gdpr-integration' ),
		'type' => 'switcher',
		'default' => 'off',
	));
			$xbox->add_field(array(
		'id' => 'linkpage-notifacation',
		'name' => __( 'Add "GDPR and Privacy Policies" page link to GDPR Notification bar?', 'gdpr-integration' ),
		'type' => 'switcher',
		'default' => 'off',
	));
	$xbox->add_field(array(
		'id' => 'linkpage-text',
		'name' => __( 'Link text', 'gdpr-integration' ),
		'type' => 'text',
		'grid' => '6-of-6',
		'default' => __('Read our GDPR and Privacy Policies', 'gdpr-integration' ),
	));
	$xbox->close_tab_item('privacy');
}
