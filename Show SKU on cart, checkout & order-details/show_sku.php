/**
 * Adds product SKU to the WooCommerce cart page
 * Uses WooCommerce 2.5 or newer
 */
add_filter( 'woocommerce_cart_item_name', 'display_sku_after_item_name', 5, 3 );
function display_sku_after_item_name( $item_name, $cart_item, $cart_item_key ) {
    $product = $cart_item['data']; // The WC_Product Object

    if( is_cart() && $product->get_sku() ) {
        $item_name .= '<br><span class="item-sku"> REF:'. $product->get_sku() . '</span>';
    }
    return $item_name;
}
/**
 * Adds product SKU to the WooCommerce checkout
 * Uses WooCommerce 2.5 or newer
 */
add_filter( 'woocommerce_checkout_cart_item_quantity', 'display_sku_after_item_qty', 5, 3 );  
function display_sku_after_item_qty( $item_quantity, $cart_item, $cart_item_key ) {
    $product = $cart_item['data']; // The WC_Product Object

    if( $product->get_sku() ) {
        $item_quantity .= '<br><span class="item-sku"> REF:'. $product->get_sku() . '</span>';
    }
    return $item_quantity;
}
/**
 * Adds product SKU to the WooCommerce order details page
 * Uses WooCommerce 2.5 or newer
 */
add_action( 'woocommerce_add_order_item_meta', 'item_sku', 10, 3 );
function item_sku( $item_id, $values, $cart_item_key ) {

  $item_sku = get_post_meta( $values[ 'product_id' ], '_sku', true );

  wc_add_order_item_meta( $item_id, 'REF', $item_sku , false );

}
