add_action( 'woocommerce_before_add_to_cart_quantity', 'woocommerce_total_product_price', 31 );

function woocommerce_total_product_price() {
    global $woocommerce, $product;
    $product_type = $product->get_type();  
  echo sprintf('<div id="product_total_price" style="margin-bottom:20px;">%s %s</div>',__('Total:','woocommerce'),'<span class="price">'.$product->get_price().'</span>');
?>
    <script>
        


        function updateCurrentPrice(productType) {
            jQuery(function($){                
                let price = <?php echo $product->get_price(); ?>;
                const currency = '<?php echo get_woocommerce_currency_symbol(); ?>';

                $('[name=quantity]').change(function(){
                    if (!(this.value < 1)) {
                        if (productType === 'variable') {
                            price = jQuery('.single_variation_wrap .woocommerce-Price-amount.amount bdi').first().contents().filter(function() {
                                return this.nodeType == 3;
                            }).text().replace(',','');
                        

                        var product_total = parseFloat(price * this.value);

                      $('#product_total_price .price').html( product_total.toFixed(2) + currency );
						//	$('.woocommerce-variation-price .price').html( product_total.toFixed(2) + currency );
							
                    }
					}
                });
            });
        }

        updateCurrentPrice('<?php echo $product_type; ?>');
		
		jQuery(function($){
			var price = <?php echo $product->get_price(); ?>,
                    currency = '<?php echo get_woocommerce_currency_symbol(); ?>';
    					$( document ).ready(function() {	
				var tis = document.getElementsByName("quantity")[0];
						 var product_total = parseFloat(price * tis.value)
							 $('#product_total_price .price').html( product_total.toFixed(2) + currency );
					     });	 
				});
    
    </script>
    <?php
}
