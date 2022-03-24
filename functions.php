<?php

/**
* ------------------------------------------------------------------------------------------------
* Add custom tabs to WooCommerce "My Account" page
* ------------------------------------------------------------------------------------------------
*/

// 1. Register new endpoint slug to use for "My Account" page
function add_custom_endpoints() {
	add_rewrite_endpoint( 'custom-endpoint1', EP_ROOT | EP_PAGES );
	add_rewrite_endpoint( 'custom-endpoint2', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'custom-endpoint3', EP_ROOT | EP_PAGES );
}
  
add_action( 'init', 'add_custom_endpoints' );
	
	
// 2. Add new query var
function endpoint_query_vars( $vars ) {
	$vars[] = 'custom-endpoint1';
	$vars[] = 'custom-endpoint2';
	$vars[] = 'custom-endpoint3';
	return $vars;
}
	
add_filter( 'woocommerce_get_query_vars', 'endpoint_query_vars', 0 );

// Flush rewrite rules on theme activation.
function custom_flush_rewrite_rules() {
	add_rewrite_endpoint( 'custom-endpoint1', EP_ROOT | EP_PAGES );
	add_rewrite_endpoint( 'custom-endpoint2', EP_ROOT | EP_PAGES );
	add_rewrite_endpoint( 'custom-endpoint3', EP_ROOT | EP_PAGES );
	flush_rewrite_rules();
}
	
add_action( 'after_switch_theme', 'custom_flush_rewrite_rules' );
	
	
// 3. Insert the new endpoint into the My Account menu
// Here we will show certain content to specific user roles
function add_customer_links_my_account( $items ) {
	$items['custom-endpoint1'] = 'Customer Tab';
	return $items;
}

function add_wholesale_links_my_account( $items ) {
	$items['custom-endpoint2'] = 'Wholesale Tab 1';
	$items['custom-endpoint3'] = 'Wholesale Tab 2';
	return $items;
}

// 4. Add content to the new endpoints

// Get current user
$user = wp_get_current_user();

// Array of available wholesale roles. I am adding 'administrator' for testing purposes
$wholesale_roles = array('wholesale1', 'wholesale2', 'wholesale3', 'administrator');

// Loop through array of available roles
if ( array_intersect( $wholesale_roles, $user->roles ) ) {

    // If user is in wholesale array, display the wholesale endpoints
	add_filter( 'woocommerce_account_menu_items', 'add_wholesale_links_my_account' );


    // Create content to go in wholesale account tabs
	function wholesale_info_content() {
        
		echo '<h3>Welcome to your wholesale account!</h3>';
		echo '<p>Here you can learn more about your wholesale account</p>';
		// if you have a custom page you can include it with wc_get_template()
		// wc_get_template( 'myaccount/wholesale-content.php' );
	}

	function wholesale_pricing_content() {
        
		echo "<h3>Info about our wholesale pricing</h3>";
        // wc_get_template( 'myaccount/pricing-content.php' );
	}

    // Add the content to the specific tabs
	// "add_action" must follow 'woocommerce_account_{your-endpoint-slug}_endpoint' format
	add_action( 'woocommerce_account_custom-endpoint2_endpoint', 'wholesale_info_content' );
	add_action( 'woocommerce_account_custom-endpoint3_endpoint', 'wholesale_pricing_content' );

} else {
    
    // If user is not in wholesale array, display the customer endpoints
	add_filter( 'woocommerce_account_menu_items', 'add_customer_links_my_account' );

	function customer_content() {
        
		echo '<h3>Welcome to your account. Thanks for being a great customer!</h3>';
	}

	add_action( 'woocommerce_account_custom-endpoint1_endpoint', 'customer_content' );
}