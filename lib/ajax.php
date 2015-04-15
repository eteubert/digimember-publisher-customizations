<?php
namespace Podlove\DigiMember;

add_action( 'wp_ajax_podlove-digimember-resume-subscription', '\Podlove\DigiMember\resume_subscription' );

function resume_subscription() {

	$purchase_id = filter_input(INPUT_POST, 'purchaseid');

	if (!$purchase_id)
		exit;

	$result = with_api(function($api) use ($purchase_id) {
		return $api->startRebilling($purchase_id);
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