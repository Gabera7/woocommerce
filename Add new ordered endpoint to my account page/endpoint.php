// 1. Register new endpoint
add_action( 'init', 'add_rastreio_endpoint' );  
function add_rastreio_endpoint() {
    add_rewrite_endpoint( 'rastreio', EP_ROOT | EP_PAGES );
}  

// ------------------
// 2. Add new query
add_filter( 'query_vars', 'rastreio_query_vars', 0 );  
function rastreio_query_vars( $vars ) {
    $vars[] = 'rastreio';
    return $vars;
}  
// ------------------
// 3. Insert the new endpoint 
add_filter( 'woocommerce_account_menu_items', 'add_rastreio_link_my_account' );
function add_rastreio_link_my_account( $items ) {
    $items['rastreio'] = 'Rastreio';
    return $items;
}  

// ------------------
// 4. Add content to the new endpoint  
add_action( 'woocommerce_account_rastreio_endpoint', 'rastreio_content' );
function rastreio_content() {
echo '<h3>Rastreio</h3><br><p>Escolha a transportadora para rastrear sua encomenda:</p>';
echo '<a href="https://www.ctt.pt/feapl_2/app/open/objectSearch/objectSearch.jspx?lang=def"  target="_blank"><img src="/wp-content/uploads/2021/05/logo-ctt.svg" alt="CTT" width="150" height="150"></a>';
echo '<a href="https://www.mrw.pt/" target="_blank"><img src="/wp-content/uploads/2021/05/MRW.png" alt="MRW" width="150" height="150" style="margin-left: 75px"></a>';
echo '<img src="/wp-content/uploads/2021/07/liana.png" alt="Voam PT" width="150" height="150" style="margin-left: 75px"';
}  



// Menu order

add_action ( 'wp_enqueue_scripts', 'child_enqueue_styles', 15);

function my_account_menu_order() {
    $menuOrder = array (
        'dashboard' => __('Painel', 'woocommerce'),
        'orders' => __('Encomendas', 'woocommerce'),
        'edit-address' => __('Moradas', 'woocommerce'),
        'edit-account' => __('Detalhes da conta', 'woocommerce'),
		'rastreio' => __('Rastreio', 'woocommerce'),
		'circulares-tecnicas' => __('Circulares Técnicas', 'woocommerce'),
        'customer-logout' => __('Sair', 'woocommerce'),
        
    );
    return $menuOrder;

}

add_filter ( 'woocommerce_account_menu_items', 'my_account_menu_order');
