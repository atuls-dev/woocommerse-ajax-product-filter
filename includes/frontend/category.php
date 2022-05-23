<?php 
	$orderby = 'name';
	$order = 'asc';
	$hide_empty = false ;
	$cat_args = array(
	    'orderby'    => $orderby,
	    'order'      => $order,
	    'hide_empty' => $hide_empty,
	);
	 
	$product_categories = get_terms( 'product_cat', $cat_args );
	 
	if( !empty($product_categories) ){
	    echo '
	 
	<ul>';
	    foreach ($product_categories as $key => $category) {
	        echo '
	 
	<li>';
	        echo '<a href="'.get_term_link($category).'" >';
	        echo $category->name;
	        echo '</a>';
	        echo '</li>';
	    }
	    echo '</ul>
	 
	 
	';
	}

?>