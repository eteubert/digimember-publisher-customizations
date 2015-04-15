<?php
namespace Podlove\DigiMember;

add_shortcode('podlove_digimember_products', '\Podlove\DigiMember\podlove_digimember_products');

function podlove_digimember_products() {

	wp_enqueue_script(
		'podlove_digimember_js',
		plugins_url('podlove_digimember_support.js', dirname(__FILE__)),
		['jquery'],
		'1.0.0',
		true
	);

	$purchase_codes = current_purchases();

	if (count($purchase_codes) === 0)
		return __('You did not buy anything yet. If you think this is wrong, please email <a href="mailto:' . get_option('admin_email') . '">' . get_option('admin_email') . '</a>.');

	$purchases = purchases_by_code_list($purchase_codes);

	return info_boxes() . implode("\n", array_map('\Podlove\DigiMember\render_purchase', $purchases));
}