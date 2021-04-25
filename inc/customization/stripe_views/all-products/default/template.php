<?php
wp_enqueue_style( 'dashicons' );
ob_start();
//Page
?>
<div class="container product-list">
	_%search_box%_
	<div class="row row-cols-1 row-cols-md-1 row-cols-lg-3 g-4">
		_%products_list%_
	</div>
	_%pagination%_
</div>
<?php
$tpl['page'] = ob_get_clean();
ob_start();
$strSearch      = __( 'Search', 'stripe-payments' );
$strClearSearch = __( 'Clear search', 'stripe-payments' );
$strViewItem    = __( 'View Details', 'stripe-payments' );
//Search box
?>
<form id="wp-asp-search-form" method="GET">
	<div class="wp-asp-listing-search-field">
		<input type="text" class="wp-asp-search-input" name="asp_search" value="_%search_term%_" placeholder="<?php echo $strSearch; ?> ...">
	<button type="submit" class="wp-asp-search-button" value="<?php echo $strSearch; ?>" title="<?php echo $strSearch; ?>"><span class="dashicons dashicons-search"></button>
	</div>
</form>
<div class="wp-asp-search-res-text">
	_%search_result_text%__%clear_search_button%_
</div>
<?php
$tpl['search_box']          = ob_get_clean();
$tpl['clear_search_button'] = ' <a href="_%clear_search_url%_">' . $strClearSearch . '</a>';
ob_start();
//Member item
?>
<div class="col">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <img src="%[product_thumb]%" alt="%[product_name]%" title="%[product_name]%">
        <span class="card-title product-name">
            %[product_name]%
        </span>
      </div>
      <div class="card-body text-center">
        <p>%[product_excerpt]%</p>
        <h3>%[product_price]%</h3>
        %[view_product_btn]%
      </div>
    </div>
</div>
<?php
$tpl['products_item']      = ob_get_clean();
$tpl['products_list']      = '';
$tpl['products_per_row']   = 3;
$tpl['products_row_start'] = '';
$tpl['products_row_end']   = '';
ob_start();
//Pagination
?>
<div class="wp-asp-pagination">
	<ul>
		_%pagination_items%_
	</ul>
</div>
<?php
$tpl['pagination'] = ob_get_clean();
//Pagination item
$tpl['pagination_item'] = '<li><a href="%[url]%">%[page_num]%</a></li>';
//Pagination item - current page
$tpl['pagination_item_current'] = '<li><span>%[page_num]%</span></li>';

//Profile button
$tpl['view_product_btn'] = '<a href="%[product_url]%" class="btn btn-primary">' . $strViewItem . '</a>';


