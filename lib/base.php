<?php
namespace Podlove\DigiMember;

function info_boxes() {
	return '
	<div id="subscription-errorbox" class="x-alert x-alert-danger x-alert-block hidden">
		<button type="button" class="close" style="right: -12px">×</button>
		<h6 class="h-alert"><i class="x-icon x-icon-flash"></i>&nbsp;&nbsp;Something went terribly wrong</h6>
		<p class="content">
			In case of trouble with cancelling or resuming the subscription, please email <a href="mailto:' . get_option('admin_email') . '">' . get_option('admin_email') . '</a>.
		</p>
	</div>

	<div id="subscription-infobox" class="x-alert x-alert-info x-alert-block hidden">
		<button type="button" class="close" style="right: -12px">×</button>
		<h6 class="h-alert"></h6>
		<p class="content"></p>
		<small>
			In case of trouble with cancelling or resuming the subscription, please email <a href="mailto:' . get_option('admin_email') . '">' . get_option('admin_email') . '</a>.
		</small>
	</div>';
}

function render_purchase($purchase) {

	$product_name = function($item) {
		return $item->product_name . ' &#x2A09; ' . $item->quantity;
	};

	$latest_transaction = $purchase->transaction_list[count($purchase->transaction_list) - 1];

	$html = '<table class="podlove_purchases">';
	$html.= '  <thead>';
	$html.= '    <tr>';
	$html.= '      <th colspan="2">' . implode(",<br>\n", array_map($product_name, $purchase->items)) . '<th>';
	$html.= '    </tr>';
	$html.= '  </thead>';
	$html.= '  <tbody>';

	$html.= '    <tr>';
	$html.= '      <td>Your Order</td>';
	$html.= '      <td>';
	$html.= '        <a href="' . $purchase->invoice_url . '" target="_blank">View Invoice</a> | ';
	$html.= '        <a href="' . $purchase->renew_url .'" target="_blank">Manage</a>';
	$html.= '      </td>';
	$html.= '    </tr>';

	$html.= '    <tr>';
	$html.= '      <td>Status</td>';
	$html.= '      <td>';
	$html.= '        <span class="billing_status">' . $purchase->billing_status_msg. '</span>';
	$html.= '        &nbsp;';
	$html.= '        <small class="billing_modify">';

	if ($purchase->billing_status === 'aborted') {
		$html .= '<a href="" class="change_subscription resume_subscription" data-action="resume" data-purchaseid="' . $purchase->id . '" data-ajaxurl="' . admin_url('admin-ajax.php') . '">resume support subscription</a>&nbsp;&nbsp;<i class="x-icon x-icon-spinner rotate hidden"></i>';
	} else {
		$html .= '<a href="" class="change_subscription cancel_subscription" data-action="cancel" data-purchaseid="' . $purchase->id . '" data-ajaxurl="' . admin_url('admin-ajax.php') . '">cancel support subscription</a>&nbsp;&nbsp;<i class="x-icon x-icon-spinner rotate hidden"></i>';
	}

	$html.= '        </small>';
	$html.= '      </td>';
	$html.= '    </tr>';

	$html.= '    <tr>';
	$html.= '      <td>Total</td>';
	$html.= '      <td>';
	if ($purchase->billing_status === 'aborted') { $html .= '<del>'; }
	$html.=          format_currency($purchase->amount, $purchase->currency) . ' per month';
	$html.= '       <small>(last payment: ' . date_i18n(get_option('date_format'), strtotime($latest_transaction->created_at)) . ')</small>';
	if ($purchase->billing_status === 'aborted') { $html .= '</del>'; }
	$html.= '      </td>';
	$html.= '    </tr>';

	$html.= '  </tbody>';
	$html.= '</table>

<style type="text/css">
@keyframes spin {
	to { transform: rotate(1turn); }
}

.rotate {
	animation: spin 1s infinite steps(60);
}
</style>

	';

	return $html;
}

function format_currency($amount, $currency) {

	if (strtoupper($currency) == 'EUR') {
		$currency = '&#x20AC;';
	}

	if (intval($amount) == $amount) {
		$amount = intval($amount);
	}

	return $amount . $currency;
}

/**
 * Get purchase codes for current user
 * 
 * @return  array
 */
function current_purchases() {
	global $wpdb;

	$current_user = wp_get_current_user();

	if ( !($current_user instanceof \WP_User) )
		return [];

	return $wpdb->get_col(
		$wpdb->prepare(
			'SELECT order_id FROM ' . $wpdb->prefix . 'digimember_user_product WHERE user_id = %d',
			$current_user->ID
		)
	);
}

function purchases_by_code_list($codes) {
	return with_api(function($api) use ($codes) {
		return array_map(function($code) use ($api) {
			return $api->getPurchase($code);
		}, $codes);
	});
}

function with_api($callback) {
	$api = \Podlove\DigiMember\PodloveDigistoreApi::connect(PODLOVE_DIGIMEMBER_API_KEY);
	$api->setLanguage('en');

	$result = $callback($api);

	$api->disconnect();

	return $result;
}
