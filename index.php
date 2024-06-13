<?php
/**
 * Plugin Name: HIBP Breach Checker
 * Plugin URI: https://github.com/GittiMcHub/wordpress-plugin-hibp-breach-check
 * Description: Provides Input Field for https://haveibeenpwned.com/ API v3/breachedaccount. If found, it will toogle an Elements visibility: [hibp_checker apikey="" breach="Facebook" toggle-safe="hibp-toggle-safe" toggle-breach="hibp-toggle-breach" input-placeholder="+49..." ]
 * Version: 1.0
 * Author: GittiMcHub
 * Author URI: https://github.com/GittiMcHub/
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

function user_form($breach, $input_placeholder, $content)
{
	$out  = '<div class="hibp-container">';
	if ( $content !== null) {
		$out .= '<div class="hibp-content">' . $content . '</div>';
	}
	$out .= '<form id="hibp-form-' . $breach . '" action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="post">';
	$out .= '<input class="hibp-input" placeholder="'. $input_placeholder .'" name="hibp_input" />';
	$out .= '<input type="submit" class="hibp-button" name="submit" value="Check" />';
	$out .= '</form></div>';
	return $out;
}

function user_feedback($toggle_element_id)
{
	$out = "";
	if ( $toggle_element_id !== null) {	
		$out .= '<script type="text/javascript">';
		$out .= "jQuery(document).ready(function() {
				function toggleVisibility(elementId) {
					var element = jQuery(\"#\" + elementId);
					if (element.is(\":visible\")) {
						element.hide();
					} else {
						element.show();
					}
				};";
		$out .= "toggleVisibility('" . $toggle_element_id . "');";
		$out .= "});";
		$out .= "</script>";
	}
	return $out;
}


function hibp_api_call($apikey, $input)
{
	$url = 'https://haveibeenpwned.com/api/v3/breachedaccount/' . $input;
	$headers = array(
		'hibp-api-key' => $apikey,
		'user-agent'   => 'HIBP Custom WordPress Plugin'
	);
	$response = wp_remote_get($url, array(
		'headers' => $headers
	));
	if (is_wp_error($response)) {
		throw new Exception('FEHLER: ' . esc_html($response->get_error_message()));
	}
	return json_decode(wp_remote_retrieve_body($response));
}

// Funktion zur Erstellung des Shortcodes
function hibp_checker_shortcode($atts = [], $content = null, $tag = '')
{
	// normalize attribute keys, lowercase
	$atts = array_change_key_case((array) $atts, CASE_LOWER);
	// override default attributes with user attributes
	$hibp_atts = shortcode_atts(
		array(
			'apikey' => '',
			'breach' => 'Facebook',
			'toggle-breach' => 'hibp-toggle-breach',
			'toggle-safe' => 'hibp-toggle-safe',
			'input-placeholder' => '+49...',
		),
		$atts,
		$tag
	);
	// During a submit
	if (isset($_POST['submit']) && isset($_POST['hibp_input'])) {
		$user_input = preg_replace('/\s+/', '', sanitize_text_field($_POST['hibp_input']));
		try {
			// get reponse
			$apiresponse = hibp_api_call($hibp_atts['apikey'], $user_input);
			
			if (!empty($apiresponse)) {
				foreach ($apiresponse as $breach) {
					if ($breach->Name === $hibp_atts['breach']) {
						return user_feedback($hibp_atts['toggle-breach']);
					}
				}
			}
			// In case API Call fails
		} catch (Exception $e) {
			return "<p>" . $e->getMessage() .  "</p>";
		}
		return user_feedback($hibp_atts['toggle-safe']);
	}
	return user_form($hibp_atts['breach'], $hibp_atts['input-placeholder'], $content);
}

function hibp_shortcode_init()
{
	wp_enqueue_style('hibp-style', plugin_dir_url(__FILE__) . 'public/hibp.css', array(), "1.0", 'all');
	wp_enqueue_script('jquery');
	add_shortcode('hibp_checker', 'hibp_checker_shortcode');
}


add_action('init', 'hibp_shortcode_init');
