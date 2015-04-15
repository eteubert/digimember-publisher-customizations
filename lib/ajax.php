<?php
namespace Podlove\DigiMember;

add_action('wp_ajax_podlove-digimember-resume-subscription', '\Podlove\DigiMember\resume_subscription');
add_action('wp_ajax_podlove-digimember-cancel-subscription', '\Podlove\DigiMember\cancel_subscription');

function resume_subscription() {
	manage_subscription('startRebilling');
}

function cancel_subscription() {
	manage_subscription('stopRebilling');
}

function manage_subscription($method) {

	if (!in_array($method, ['startRebilling', 'stopRebilling']))
		die("Must be one of: 'startRebilling', 'stopRebilling'");

	if (!$purchase_id = filter_input(INPUT_POST, 'purchaseid'))
		exit;

	// fixme: check that current user owns purchase

	$result = with_api(function($api) use ($purchase_id, $method) {
		return call_user_func([$api, $method], $purchase_id);
	});

	respond_with_json($result);
}

function respond_with_json($result) {
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	echo json_encode($result);
	die();
}