<?php

add_action( 'wp_enqueue_scripts', 'martfury_child_enqueue_scripts', 20 );
function martfury_child_enqueue_scripts() {
	wp_enqueue_style( 'martfury-child-style', get_stylesheet_uri() );
	if ( is_rtl() ) {
		wp_enqueue_style( 'martfury-rtl', get_template_directory_uri() . '/rtl.css', array(), '20180105' );
	}
}

// always display rating stars
function filter_woocommerce_product_get_rating_html( $rating_html, $rating ) {
    $rating_html  = '<div class="star-rating">';
    $rating_html .= wc_get_star_rating_html( $rating );
    $rating_html .= '</div>';

    return $rating_html;
};
add_filter( 'woocommerce_product_get_rating_html', 'filter_woocommerce_product_get_rating_html', 10, 3 );

// For Woocommerce version 3 and above only
add_filter( 'woocommerce_loop_add_to_cart_link', 'filter_loop_add_to_cart_link', 20, 3 );
function filter_loop_add_to_cart_link( $button, $product, $args = array() ) {
    if( $product->is_in_stock() ) return $button;

    // HERE set your button text (when product is not on stock)
    $button_text = __('Not available', 'woocommerce');

    // HERE set your button STYLING (when product is not on stock)
    $color = "#000";      // Button text color
    $background = "#c5c5c5"; // Button background color

    // Changing and disbling the button when products are not in stock
    $style = 'color:'.$color.';background-color:'.$background.';cursor:not-allowed;';
    return sprintf( '<a class="button disabled" style="%s"><i class="p-icon icon-bag2" data-rel="tooltip" title="Add to cart"></i>%s</a>', $style, $button_text );
}

//Remove all possible fields

function wc_remove_checkout_fields( $fields ) {

    // Billing fields
    unset( $fields['billing']['billing_company'] );
    unset( $fields['billing']['billing_country'] );
    //unset( $fields['billing']['billing_email'] );
    //unset( $fields['billing']['billing_phone'] );
    //unset( $fields['billing']['billing_state'] );
    //unset( $fields['billing']['billing_first_name'] );
    unset( $fields['billing']['billing_last_name'] );
    //unset( $fields['billing']['billing_address_1'] );
    unset( $fields['billing']['billing_address_2'] );
    //unset( $fields['billing']['billing_city'] );
    //unset( $fields['billing']['billing_postcode'] );

    // Shipping fields
    unset( $fields['shipping']['shipping_company'] );
    unset( $fields['shipping']['shipping_country'] );
    //unset( $fields['shipping']['shipping_phone'] );
    //unset( $fields['shipping']['shipping_state'] );
    //unset( $fields['shipping']['shipping_first_name'] );
    unset( $fields['shipping']['shipping_last_name'] );
    //unset( $fields['shipping']['shipping_address_1'] );
    unset( $fields['shipping']['shipping_address_2'] );
    //unset( $fields['shipping']['shipping_city'] );
    //unset( $fields['shipping']['shipping_postcode'] );

    // Order fields
    //unset( $fields['order']['order_comments'] );

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'wc_remove_checkout_fields' );

function wc_unrequire_wc_phone_field( $fields ) {
    $fields['billing_email']['required'] = false;
    return $fields;
}
add_filter( 'woocommerce_billing_fields', 'wc_unrequire_wc_phone_field');

function custom_override_checkout_fields($fields){
    $fields['billing']['billing_first_name']['label'] = 'Full Name';
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');

function custom_remove_my_account_links( $menu_links ){
	//unset( $menu_links['edit-address'] ); // Addresses
	//unset( $menu_links['dashboard'] ); // Remove Dashboard
	//unset( $menu_links['payment-methods'] ); // Remove Payment Methods
	//unset( $menu_links['orders'] ); // Remove Orders
	unset( $menu_links['downloads'] ); // Disable Downloads
	//unset( $menu_links['edit-account'] ); // Remove Account details tab
	//unset( $menu_links['customer-logout'] ); // Remove Logout link
	return $menu_links;
}
add_filter ( 'woocommerce_account_menu_items', 'custom_remove_my_account_links' );
// function custom_reorder_checkout_fields( $checkout_fields ) {
// 	$checkout_fields['billing']['billing_address_1']['priority'] = 4;
// 	return $checkout_fields;
// }
// add_filter( 'woocommerce_checkout_fields', 'custom_reorder_checkout_fields' );

add_action( 'wp_footer', 'google_footer_script', 100 );
function google_footer_script(){
  ?>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-SRKGMPW6VE"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-SRKGMPW6VE');
  </script>
  <?php
}

add_action( 'woocommerce_register_form_start', 'woocom_extra_register_fields' );
function woocom_extra_register_fields() {?>

  <p class="form-row form-row-wide">
    <input required type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" placeholder="<?php _e( 'Your name', 'woocommerce' ); ?>"/>
  </p>
  <?php
}

add_action('woocommerce_created_customer', 'woocom_save_extra_register_fields');
function woocom_save_extra_register_fields($customer_id) {

  if (isset($_POST['billing_first_name'])) {
    update_user_meta($customer_id, 'billing_first_name', sanitize_text_field($_POST['billing_first_name']));
  }
}

add_filter( 'woocommerce_product_add_to_cart_text' , 'custom_woocommerce_product_add_to_cart_text' );
/**
 * custom_woocommerce_template_loop_add_to_cart
*/
function custom_woocommerce_product_add_to_cart_text() {
  global $product;
  $product_type = $product->product_type;
  foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
		$_product = $values['data'];

		if( get_the_ID() == $_product->id ) {
			return __('Already in cart', 'woocommerce');
		}
	}
  switch ( $product_type ) {
      case 'external':
          return __( 'Buy product', 'woocommerce' );
      break;
      case 'grouped':
          return __( 'View products', 'woocommerce' );
      break;
      case 'simple':
          return __( 'Add to cart', 'woocommerce' );
      break;
      case 'variable':
          return __( 'View product', 'woocommerce' );
      break;
      default:
          return __( 'Read more', 'woocommerce' );
  }
  return $product_type;
}

/**
 * Change the add to cart text on single product pages
 */
add_filter('woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text');

function woo_custom_cart_button_text() {

	foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
		$_product = $values['data'];

		if( get_the_ID() == $_product->id ) {
			return __('Already in cart - Add Again?', 'woocommerce');
		}
	}

	return __('Add to cart', 'woocommerce');
}

// /**
//  * Change the add to cart text on product archives
//  */
// add_filter( 'woocommerce_product_add_to_cart_text', 'woo_archive_custom_cart_button_text' );

// function woo_archive_custom_cart_button_text() {

// 	foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
// 		$_product = $values['data'];

// 		if( get_the_ID() == $_product->id ) {
// 			return __('Already in cart', 'woocommerce');
// 		}
// 	}

// 	return __('Add to cart', 'woocommerce');
// }