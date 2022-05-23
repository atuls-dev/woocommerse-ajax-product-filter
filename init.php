<?php
/**
 * Plugin Name: Woo Products Filter 
 * Plugin URI: https://github.com/atuls-dev
 * Description: A custom filter for woocommerce .
 * Version: 1.0.0
 * Author: Atul
 * Author URI: https://github.com/atuls-dev
 * Text Domain: woo-products-filter 
 * */ 
global $WooProductFilter;
define('WPF', plugin_dir_url(__FILE__));
define('WPF_PATH', plugin_dir_path(__FILE__));
define('WPF_TEXTDOMAIN', 'woo-product-filter');
defined('WPF_INCLUDE_PATH')  OR define('WPF_INCLUDE_PATH', WPF_PATH . 'includes/');
require WPF_INCLUDE_PATH . "lib/widget.php";
class WooProductFilter { 

	public function __construct() {
		register_activation_hook(__FILE__, array(__CLASS__, 'wpf_activated'));
        register_deactivation_hook(__FILE__, array(__CLASS__, 'wpf_deactivated'));
        add_action("init", [$this, "init_wpf_product_filter"]);
        
        add_action('wp_enqueue_scripts', array($this, 'wpf_assets'));
        add_action('admin_enqueue_scripts', array($this, 'wpf_admin_assets'));
        add_action("admin_menu", [$this, "wpf_add_menu_pages"]);
        //add_shortcode('WPF-CATEGORY',array($this,'wpf_category_filter'));

        add_action("wp_ajax_wpf_cat_filter", array($this, "wpf_cat_filter_fn"));
        add_action("wp_ajax_nopriv_wpf_cat_filter", array($this, "wpf_cat_filter_fn"));

        // woocommerce shop page top filters
        add_action( 'woocommerce_archive_description', array($this, "action_woocommerce_before_shop_loop"), 10 ); 

        add_action("wp_ajax_wpf_filter_products", array($this, "fn_wpf_filter_products"));
        add_action("wp_ajax_nopriv_wpf_filter_products", array($this, "fn_wpf_filter_products"));

        add_action( 'wp', [$this,'fn_remove_default_sorting_storefront'] );

        //add_action("admin_init", array($this,"display_cfc_panel_fields"));
        //add_action("wp_ajax_import_xlsx", array($this, "import_xlsx_fn"));
        //add_action("wp_ajax_nopriv_import_xlsx", array($this, "import_xlsx_fn"));
        //add_action("wp_ajax_update_tab_content", array($this, "update_tab_content_fn"));
        //add_action("wp_ajax_nopriv_update_tab_content", array($this, "update_tab_content_fn"));
        //add_action("wp_ajax_cf_submit_form", array($this, "cf_submit_form_fn"));
        //add_action("wp_ajax_nopriv_cf_submit_form", array($this, "cf_submit_form_fn"));

	}

	public function wpf_activated() {
        require_once( WPF_INCLUDE_PATH . "lib/activation.php");
    }

    public function wpf_deactivated() {
        require_once( WPF_INCLUDE_PATH . "lib/deactivation.php");
    }

    public function init_wpf_product_filter()
    {
        require WPF_INCLUDE_PATH . "settings.php";

    }

    public function wpf_add_menu_pages()
    {
        add_menu_page(
          "WooProductsFilter",
          "WooProductsFilter",
          "manage_options",
          "wpf_main_page",
          [$this, "wpf_main_page_page_fn"],
          "dashicons-book",
          12
        );
    }

    public function wpf_main_page_page_fn()
    {
    ?>
        <style type="text/css">
            .nl-esi-shadow .sec-title {
                border: 1px solid #ebebeb;
                background: #fff;
                color: #d54e21;
                padding: 2px 4px;
            }
            .nl-esi-shadow{
                border:1px solid #ebebeb; padding:5px 20px; background:#fff; margin-bottom:40px;
                -webkit-box-shadow: 4px 4px 10px 0px rgba(50, 50, 50, 0.1);
                -moz-box-shadow:    4px 4px 10px 0px rgba(50, 50, 50, 0.1);
                box-shadow:         4px 4px 10px 0px rgba(50, 50, 50, 0.1);
            }
        </style>
        <div class="wrap">
            <h1>Woo Product Filter</h1>
            <form method="post" action="options.php" class="nl-esi-shadow">
                <?php
                settings_fields("wpf-options");
                do_settings_sections("wpf-plugin-options");
                submit_button();?>
            </form>
        <?php
    }

	public function wpf_assets(){
        global $wp;
        $current_url = home_url( add_query_arg( array(), $wp->request ) );

        $price_range = $this->getPriceRange();

        $min_price = ( isset($_GET['min_price']) ) ? $_GET['min_price'] : '';
        $max_price = ( isset($_GET['max_price']) ) ? $_GET['max_price'] : '';

        //wp_register_style('cfc_fontqwesome', 'https://pro.fontawesome.com/releases/v5.10.0/css/all.css', array(), time(), 'All');
        //wp_enqueue_style('cfc_fontqwesome');
        wp_register_style('wpf_css', WPF . 'assets/css/style.css', array(), time(), 'All');
        wp_enqueue_style('wpf_css');

        wp_register_script('wpf_jquery_ui', WPF . 'assets/js/jquery-ui.min.js', array('jquery'), time(), true);
        wp_enqueue_script('wpf_jquery_ui');

        wp_register_script('wpf_script', WPF . 'assets/js/script.js', array('jquery'), time(), true);
        wp_enqueue_script('wpf_script');
        wp_localize_script('wpf_script', 'wpf_ajax', array('ajaxurl' => admin_url('admin-ajax.php'), 'current_url' => $current_url, 'price_range' => $price_range, 'min_price' => $min_price, 'max_price' => $max_price ));
	       
        //select2 scripts
        wp_register_style('wpf_select2_css', WPF . 'assets/css/select2.min.css', array(), time(), 'All');
        wp_enqueue_style('wpf_select2_css');
        wp_register_script('wpf_select2_js', WPF . 'assets/js/select2.min.js', array('jquery'), time(), true);
        wp_enqueue_script('wpf_select2_js');

    }

	public function wpf_admin_assets(){
   		//wp_register_script('cfc_form_ajax_js', WPF . 'assets/js/jquery.form.min.js', array('jquery'), time(), true);
       // wp_enqueue_script('cfc_form_ajax_js');
        wp_register_script('wpf_admin_script', WPF . 'assets/js/admin-script.js', array('jquery'), time(), true);
        wp_enqueue_script('wpf_admin_script');
        wp_localize_script('wpf_admin_script', 'wpf_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));
	}

 
	public function wpf_category_filter($atts = [])
    {

        global $wpdb;
        //$current_category = get_queried_object();

        $cat = get_queried_object();
        if (is_a($cat, 'WP_Term')) {
            $cat_id = ( $cat->taxonomy == 'product_cat' ) ? $cat->term_id : '';
        }else{
            $cat_id = '';
        }

        ob_start();
            echo '<input type="hidden" name="productCatFilter" class="productCatFilter" value="'.$cat_id.'" >';
            
            $this->getCategoriesList(0);
            echo '<div class="mobCatFilter">';
                echo '<div class="mobBack"></div>';
                $this->getMobileCategories(0);
            echo '</div>';
        return ob_get_clean();
    }

    public function getCategoriesList( $parent_cat_id ) {

        //$current_category = get_queried_object();

        $cat = get_queried_object();
        if (is_a($cat, 'WP_Term')) {
            $cat_id = ( $cat->taxonomy == 'product_cat' ) ? $cat->term_id : '';
        }else{
            $cat_id = '';
        }

        //echo "<pre>";
        //$cat_children = get_term_children($current_category->term_id, 'product_cat');
        $parent_ids = get_ancestors($cat_id, 'product_cat', 'taxonomy');

        $args = array(
           'hierarchical' => 1,
           'show_option_none' => '',
           'hide_empty' => true,
           'parent' => $parent_cat_id,
           'taxonomy' => 'product_cat'
        );
        $subcats = get_categories($args);

        if ( $parent_cat_id == 0 ){
            $li_class = 'productCategoryList';
        }else{
            $li_class = 'sub_cat sub_cat_'.$parent_cat_id;
            //var_dump($_POST['parent_id']);die;
            $super_parent_id = get_term( $cat_id )->parent;
            $li_class .= ( ( $cat_id == $parent_cat_id || in_array( $parent_cat_id,$parent_ids) ) ? ' show_sub ' : '' );
        }
        if ( $subcats ) { // added if statement to check if there are subcategories
            echo '<ul class="' . $li_class . ' ">';
                foreach ($subcats as $sc) {
                    $link = get_term_link( $sc->slug, $sc->taxonomy );
                    $has_children = get_terms( $sc->taxonomy, array(
                        'parent'    => $sc->term_id,
                        'hide_empty' => true
                        ) 
                    );

                    $activeClass = ( $cat_id == $sc->term_id ? 'active' : '' );
                    $subClass = ( !empty($has_children) ) ? "hasSubItem" :  "";
                    echo '<li class="catItem '.$subClass. ' ' . $activeClass .'" data-id="'.$sc->term_id.'" data-name="'.$sc->name.'" ><a href="'.$link.'">'.$sc->name.'</a></li>';
                    $this->getCategoriesList( $sc->term_id ); // function calls itself
                }
            echo '</ul>';
        } else {
            return; //return if no subcategories
        }
    } // last bracket kept being pushed out of code block if I used a line break

    public function getMobileCategories($parent_cat_id) {
        $args = array(
           'hierarchical' => 1,
           'show_option_none' => '',
           'hide_empty' => true,
           'parent' => $parent_cat_id,
           'taxonomy' => 'product_cat'
        );
        $subcats = get_categories($args);
        if ( $subcats ) { // added if statement to check if there are subcategories
            echo '<ul class="productCategoryMobList">';
                foreach ($subcats as $sc) {
                    $link = get_term_link( $sc->slug, $sc->taxonomy );
                    echo '<li class="catItemMob" data-id="'.$sc->term_id.'" data-parent_id="'.$parent_cat_id.'" data-name="'.$sc->name.'" >'.$sc->name.'</li>';
                }
            echo '</ul>';
        } else {
            return; //return if no subcategories
        }
    }

    public function getMobileSubCategories($parent_cat_id) {
        $args = array(
           'hierarchical' => 1,
           'show_option_none' => '',
           'hide_empty' => true,
           'parent' => $parent_cat_id,
           'taxonomy' => 'product_cat'
        );
        $subcats = get_categories($args);
        $subhtml = '';
        $subClass = ( empty($parent_cat_id) ? '': 'sub-cat' );
        if ( $subcats ) { // added if statement to check if there are subcategories
            foreach ($subcats as $sc) {
                $link = get_term_link( $sc->slug, $sc->taxonomy );
                $subhtml .= '<li class="catItemMob" data-id="'.$sc->term_id.'" data-parent_id="'.$parent_cat_id.'" data-name="'.$sc->name.'" >'.$sc->name.'</li>';
            }
            return $subhtml; 
        } else {
            return; //return if no subcategories
        }
    }

    public function wpf_cat_filter_fn() {
        $cat_id = (int) $_POST['cat_id'];
        $cat_name = get_term( $cat_id )->name;

        //echo "<pre>"; print_r( get_term( $cat_id ) ); echo "</pre>";

        $parent_id = (int) $_POST['parent_id'];
        //var_dump($_POST['parent_id']);die;
        $super_parent_id = get_term( $parent_id )->parent;

        $subcat = $this->getMobileSubCategories($cat_id);
        
        $response = array(
            'success'   => ( empty($subcat) ? 'false' : 'true'), 
            'html_list' => $subcat,
            'cat_id'    => $cat_id,
            'cat_name'  => $cat_name,
            'back_btn'  => ( $_POST['step'] == 'back' && $_POST['parent_id'] == '' ) ? '' : '<button class="catItemMob" type="button" data-id="'.$parent_id.'" data-parent_id="'.$super_parent_id.'" data-step="back" > &#8592;&nbsp;'.$cat_name.'</button>'
        );

        echo json_encode( $response );
        wp_die();
    }

        // define the woocommerce_before_shop_loop callback 
    public function action_woocommerce_before_shop_loop() {

        $cat = get_queried_object();
        if (is_a($cat, 'WP_Term')) {
            if ( $cat->taxonomy == 'pwb-brand' ){
                return;
            }
        }

        if( ( !is_shop() && !is_product_category() ) ){
            return;
        }

        $orderby_arr = array(
            'popularity' => 'Most Popular',
            'date' => 'Newest',
            'price' => 'Lowest Price',
            'price-desc' => 'Highest Price',
        );

        // make action magic happen here... 
        //ob_start();
        ?>
            <!-- Sort by filter starts -->
            <?php $active_sortby = ( isset($_GET['orderby']) && !empty($_GET['orderby']) ) ? $_GET['orderby'] : ''; 
            if( !empty($orderby_arr) ) { ?>
              <div class="wpf_fltr_wrap ">
                <select class="wpfFilter wpfSortFilter" name="wpfSortFilter">
                    <option value=''>Sort by</option>
                    <?php foreach ($orderby_arr as $opt => $optname) { ?>
                        <option <?php echo ( $active_sortby == $opt ) ? 'selected="selected"' : ''; ?> value="<?php echo $opt; ?>"><?php echo $optname; ?></option>
                    <?php } ?>
                </select>
              </div>
            <?php } ?>
            <!-- Sort by filter ends -->

            <!-- Brands filter starts -->
            <?php
            $cat = get_queried_object();
            if (is_a($cat, 'WP_Term')) {
                $brand_slug = ( $cat->taxonomy == 'pwb-brand' ) ? $cat->slug : '';
            }

            echo '<div class="wpf_fltr_wrap attr_brand_wrap">';
            if( !empty($brand_slug)) {
                echo '<input type="hidden" name="wpfBrand" value="'.$brand_slug.'">';
            }else{
                $brands_arr = $this->getBrandsData();
                $active_brands = ( isset($_GET['pwb-brand-filter']) && !empty($_GET['pwb-brand-filter']) ) ? explode(',',$_GET['pwb-brand-filter']) : [];
                echo $this->getBrandsList($brands_arr,$active_brands);
            }
            echo '</div>';
            //Brands filter ends 

            //Attributes Filter starts 
           
            echo $this->wpf_attributes_filter_list();
            
            // $attr_arr = $this->getAttrTaxonomies();
            // if( !empty($attr_arr) ) { 
        
            //     foreach($attr_arr as $name => $slug ) {
            //         //echo "<pre>";print_r($name);echo "</pre>";
            //         $taxTerms = $this->getTaxonomyTerms($slug);
            //         if( !empty($taxTerms)):
            //             echo '<div class="wpf_fltr_wrap attr_'.$slug.'_wrap ">';
            //             echo '<select class="wpfFilter wpfTaxonomy wpf_fltr" name="'.$name.'" data-slug="'.$slug.'" multiple="multiple" data-placeholder="'.$name.'" >';
            //                 echo '<option value="">'.ucfirst($name).'</option>';
            //             foreach($taxTerms as $term) {
            //                // echo "<pre>";print_r($_GET['filter_'.$name]);echo "</pre>"; die('vvv');
            //               $active_attr = ( isset($_GET['filter_'.$name]) && !empty($_GET['filter_'.$name]) ) ? $_GET['filter_'.$name] : '';
            //                 ?>
                      <!--      <option <?php echo ( $active_attr == $term->slug ) ? 'selected="selected"' : ''; ?> value="<?=$term->slug?>" data-id="<?=$term->term_id?>"><?php echo $term->name ?></option> -->
                        <?php
            //             }
            //             echo '</select></div>';
            //         endif;
            //     }

            // }
            ?>
            <!-- Attributes Filter ends -->

            <!-- Price Range Filter  -->
            <?php
            $price_range = $this->getPriceRange();
            $min_price = ( isset($_GET['min_price']) ) ? $_GET['min_price'] : '';
            $max_price = ( isset($_GET['max_price']) ) ? $_GET['max_price'] : '';
            if( !empty($price_range) ) { ?>
                <div class="wpf-price-dropdown">
                    <button class="wpfPriceBtn">Price</button>
                    <div id="wpfPriceDropdown" class="wpf-dropdown-content">
                        <!-- <input type="range" id="wpfPriceRange" name="wpfPriceRange" min="<?php echo (int) $price_range->min_price; ?>" max="<?php echo (int) $price_range->max_price; ?>">
                        <button>Filter</button> -->
                        <form id="wpfPriceForm" >

                          <!--   <input type="checkbox" id="wpfPriceActive" <?php echo ( isset($_GET['min_price']) || isset($_GET['max_price']) ) ? 'checked="checked"' : ''; ?> > -->
                            <div class="price-range-slider">
                                <p class="range-value">
                                    <label><?php echo get_woocommerce_currency_symbol(); ?></label>
                                    <input type="text" id="wpfMinPrice" name="min_price" value="<?php echo (int) $price_range->min_price; ?>" readonly>
                                    <span>-</span>
                                    <label><?php echo get_woocommerce_currency_symbol(); ?></label>
                                    <input type="text" id="wpfMaxPrice" name="max_price" value="<?php echo (int) $price_range->max_price; ?>" readonly>
                                </p>
                                <div id="wpfSliderRange" class="range-bar"></div>
                                <!-- <input type="reset" id="wpfPriceResetBtn" class="wpf-filter-btn" name="Reset"> -->

                                <label class="wpfSwitch">
                                    <input type="checkbox" id="wpfPriceActive" <?php echo ( isset($_GET['min_price']) || isset($_GET['max_price']) ) ? 'checked="checked"' : ''; ?> >
                                    <span class="slider round"></span>
                                </label>
                                <input type="submit" id="wpf-filter-btn" class="wpf-filter-btn" name="Filter" value="Filter" >

                            </div>
                        </form>

                    </div>
                </div>

            <?php 
            }

            //$this->get_attributes_list();
            //echo ob_get_clean();

    }

     public function current_products_query($cat_id=null)
    {

        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'product',
            'tax_query' => array(
                array(
                    'taxonomy' => 'pwb-brand',
                    'operator' => 'EXISTS'
                )
            ),
            'fields' => 'ids',
        );

        if ( $cat_id != null ) {
            $cat = get_term($cat_id);
            $cat_id         = $cat->term_id;
            $cat_id_array   = get_term_children($cat_id, $cat->taxonomy);
            $cat_id_array[] = $cat_id;
            $args['tax_query'][] = array(
                'taxonomy' => $cat->taxonomy,
                'field'    => 'term_id',
                'terms'    => $cat_id_array
            );
        }else{
            $cat = get_queried_object();

            if (is_a($cat, 'WP_Term')) {
                $cat_id                 = $cat->term_id;
                $cat_id_array   = get_term_children($cat_id, $cat->taxonomy);
                $cat_id_array[] = $cat_id;
                $args['tax_query'][] = array(
                    'taxonomy' => $cat->taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $cat_id_array
                );
            }
        }

        if (get_option('woocommerce_hide_out_of_stock_items') === 'yes') {
            $args['meta_query'] = array(
                array(
                    'key'     => '_stock_status',
                    'value'   => 'outofstock',
                    'compare' => 'NOT IN'
                )
            );
        }

        $wp_query = new WP_Query($args);
        wp_reset_postdata();

        return $wp_query->posts;
    }

    public function get_product_ids_existing_brands($args) {

        $args['posts_per_page'] = -1;
        $args['tax_query'][] = array(
                    'taxonomy' => 'pwb-brand',
                    'operator' => 'EXISTS'
                );
        $args['fields'] = 'ids';

        if (get_option('woocommerce_hide_out_of_stock_items') === 'yes') {
            $args['meta_query'] = array(
                array(
                    'key'     => '_stock_status',
                    'value'   => 'outofstock',
                    'compare' => 'NOT IN'
                )
            );
        }


        $wp_query = new WP_Query($args);
        wp_reset_postdata();

        return $wp_query->posts;
    }


    public function get_products_brands($product_ids)
    {

        $product_ids = implode(',', array_map('intval', $product_ids));

        global $wpdb;

        $brand_ids = $wpdb->get_col("SELECT DISTINCT t.term_id
            FROM {$wpdb->prefix}terms AS t
            INNER JOIN {$wpdb->prefix}term_taxonomy AS tt
            ON t.term_id = tt.term_id
            INNER JOIN {$wpdb->prefix}term_relationships AS tr
            ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tt.taxonomy = 'pwb-brand'
            AND tr.object_id IN ($product_ids)
        ");

        return ($brand_ids) ? $brand_ids : false;
    }
             
    public function getBrandsData($args=array(), $cat_id=null) {

        $result_brands = array();

        if( !empty($args) ) {
            $existing_products = $this->get_product_ids_existing_brands($args);
        }else{
            $existing_products = $this->current_products_query($cat_id);
        }
        
        //obtains brands ids
        if (!empty($existing_products)) {
            $brands = $this->get_products_brands($existing_products);
        }else{
            $brands =  get_terms('pwb-brand', array('hide_empty' => true, 'fields' => 'ids'));
            // $args = array(
            //    'hierarchical' => 1,
            //    'show_option_none' => '',
            //    'hide_empty' => 1,
            //    'parent' => 0,
            //    'taxonomy' => 'pwb-brand'
            // );

            // $result_brands = get_categories($args);
        }

        foreach( $brands as $brand ) {
            $result_brands[] = get_term($brand);
        }

        // echo "<pre>";
        // print_r($result_brands);
        // die;

        return $result_brands;
    } // last bracket kept being pushed out of code block if I used a line break


    public function getBrandsList( $brands, $active_brands=array() ) {
        $out = '';
        ob_start();

        //$active_brands = ( isset($_GET['pwb-brand-filter']) && !empty($_GET['pwb-brand-filter']) ) ? $_GET['pwb-brand-filter'] : ''; 
        if( !empty($brands) ) { ?>
            <select class="wpfFilter wpfBrand wpf_fltr" name="wpfBrand" data-slug="brand"  multiple="multiple">
                <option value="">Brand</option>
            <?php foreach ($brands as $brand) { ?>
                <option <?php echo ( in_array($brand->slug,$active_brands) ) ? 'selected="selected"' : ''; ?> value="<?php echo $brand->slug; ?>" data-id="<?php echo $brand->term_id; ?>"><?php echo $brand->name; ?></option>
            <?php } ?>
            </select>
        <?php
        } 
        $out .= ob_get_clean();
        return $out;
    } 

    public function getPriceRange() {
        global $wpdb;
        
        $sql = "
            SELECT min( min_price ) as min_price, MAX( max_price ) as max_price
            FROM {$wpdb->wc_product_meta_lookup}
            WHERE product_id IN (
                SELECT ID FROM {$wpdb->posts}
                WHERE {$wpdb->posts}.post_type IN ('product') AND {$wpdb->posts}.post_status = 'publish'
            ) AND stock_status = 'instock' ";
        
        return $wpdb->get_row( $sql );
    }

    public function fn_wpf_filter_products() {
        $filter_type = '';
        $query_string_arr = array();
        if ( isset($_POST['filter_type']) && !empty($_POST['filter_type']) ) {
            $filter_type = $_POST['filter_type'];
        }
        
        //variation level out of stock parent ids starts
        $outofstock_products = array();
        $var_args = array(
            'post_type'     => 'product_variation',
            'meta_query'    => array(
                'relation' => 'AND',
                array(
                    'key'     => '_stock_status',
                    'value'   => 'outofstock',
                    'compare' => '=',
                )
            ),
            'fields'         => 'id=>parent',
            'posts_per_page' => -1, 
            'groupby'        => 'post_parent', 
        );

        if( isset($_POST['attr']) && !empty($_POST['attr']) ) {
            foreach ( $_POST['attr'] as $tax => $term_slug ) {
                if( !empty($term_slug) ) {
                    $var_args['meta_query'][] = array(
                        'key'     => 'attribute_'.$tax,
                        'value'   => $term_slug,
                        'compare' => 'IN',
                    );
                }
            }
            $outofstock = new WP_Query( $var_args );
            $outofstock_products = wp_list_pluck( $outofstock->posts, 'post_parent' );
        }
        //variation level out of stock parent ids ends

        $posts_per_page = ( wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );
       
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => ( $posts_per_page ? $posts_per_page : 9 ),
            'orderby' => 'name',
            'order' => 'asc',
            'post__not_in' => $outofstock_products
        );

        $args['paged'] =  ( isset($_POST['paged']) && !empty($_POST['paged']) ) ? intval($_POST['paged']) : 1;
        // echo "<pre>";
        // print_r($args);
        // echo "</pre>";
        // die;

        if ( isset($_POST['sort_by']) && !empty($_POST['sort_by']) ) {

            switch ($_POST['sort_by']) {
                case 'popularity':
                    $args['meta_key'] = 'total_sales';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'desc';
                break;
                case 'date':
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'desc';
                break;
                case 'price':
                    $args['meta_key'] = '_price';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'asc'; 
                break;
                case 'price-desc':
                    $args['meta_key'] = '_price';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'desc';
                break;
            } 
            $query_string_arr['orderby'] = $_POST['sort_by'];

        }

        $args['tax_query'] = array(
            'relation' => 'AND',
            array(
                    'taxonomy'      => 'product_visibility',
                    'field'         => 'name',
                    'terms'         => array( 'outofstock', 'exclude-from-search' ), 
                    'operator'      => 'NOT IN'
            )
        );


        $wpf_url = get_permalink( wc_get_page_id( 'shop' ) );
        $wpf_page_title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );
        if ( isset($_POST['cat_id']) && !empty($_POST['cat_id']) ) {
            $wpf_url = get_term_link( (int) $_POST['cat_id'], 'product_cat' );
            $args['tax_query'][] = array(
                    'taxonomy'  => 'product_cat',
                    'field'     => 'term_id',
                    'terms'     => array($_POST['cat_id']),
                    'operator'  => 'IN',
            );
            $wpf_page_title = get_term( $_POST['cat_id'] )->name;
        }
        $category_args = $args;

        if( isset($_POST['price_range']) && !empty($_POST['price_range']) ) {

            $args['meta_query'][] = array(
                    'key' => '_price',
                    'value' => $_POST['price_range'],
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC'
                );
            $query_string_arr['min_price'] = $_POST['price_range'][0];
            $query_string_arr['max_price'] = $_POST['price_range'][1];
        }

        $brands_args = $args;
        $active_brands = array();
        if ( isset($_POST['brands']) && !empty($_POST['brands']) ) {
            $active_brands = $_POST['brands'];
            $args['tax_query'][] = array(
                'taxonomy'      => 'pwb-brand',
                'field'         => 'slug',
                'terms'         => $_POST['brands'], 
                'operator'      => 'IN'
            );
            $query_string_arr['pwb-brand-filter'] = implode(',',$_POST['brands']);
        }

        $attr_args = $args;
        $active_attr_filters = array();
        if( isset($_POST['attr']) && !empty($_POST['attr']) ) {
            $active_attr_filters = $_POST['attr'];
            foreach ( $_POST['attr'] as $tax => $term_slug ) {
                if( !empty($term_slug) ) {
                    $brands_args['tax_query'][] = $args['tax_query'][] = array(
                        'taxonomy'      => $tax,
                        'field'         => 'slug',
                        'terms'         => $term_slug, 
                        'operator'      => 'IN'
                    );
                    if ( $tax == $filter_type ) {
                        $attr_args['tax_query'][] = array(
                            'taxonomy'      => $tax,
                            'field'         => 'slug',
                            'terms'         => $term_slug, 
                            'operator'      => 'IN'
                        );
                    }
                    $query_string_arr[$tax] = implode(',',$term_slug);
                }
            }
        }

      
        $args = ( $filter_type == 'cat' ) ? $category_args : $args;
        
        $loop = new WP_Query( $args );
        if ( $loop->have_posts() ) {
            ob_start();
            while ( $loop->have_posts() ) : $loop->the_post();
                wc_get_template_part( 'content', 'product' );
            endwhile;
            $products_html =  ob_get_clean();

            $total_pages = $loop->max_num_pages;
            $pagination = '';
            if ($total_pages > 1){
                $current_page = max(1, $args['paged']);
                $page_args = array(
                    'base' => get_pagenum_link(1) . '%_%',
                    'total' => $total_pages,
                    'current' => $current_page,
                    'format' => '/page/%#%/',
                );

                ob_start();
                    wc_get_template( 'loop/pagination.php', $page_args );
                $pagination = ob_get_clean();
            }

        } else {
            $products_html = 'No products found...';
        }
        wp_reset_postdata();

        $response = array(
                'success'=>'true',
                'products_html'=> $products_html,
               
                'breadcrumbs' => $this->get_breadcrumbs( (int) $_POST['cat_id'] ),
                'pagination' => $pagination, 
            );

        if ( !empty($filter_type) ) {

            //Adoptive brands list 
            if ( $filter_type != 'brand' ) {
                if ( $filter_type == 'cat' ) {
                    $brands_arr = $this->getBrandsData(array(), $_POST['cat_id'] );
                    $response['brands_list'] = $this->getBrandsList( $brands_arr );
                }else{
                    $brands_arr = $this->getBrandsData($brands_args);
                    $response['brands_list'] = $this->getBrandsList( $brands_arr,$active_brands );
                }
            }

            //Adoptive attribute list
            if ( $filter_type == 'cat' ) {
                $attr_arr = $this->get_existing_attr_filter_arr($attr_args);
                $attr_list = $this->get_wpf_attributes_filter_list($attr_arr);    
            } else {
                $attr_arr = $this->get_existing_attr_filter_arr($attr_args);
                $attr_list = $this->get_wpf_attributes_filter_list($attr_arr,$active_attr_filters);
            }

            // echo "<pre>";
            // print_r($active_attr_filters);
            // echo "</pre>";
            // die;
            if( empty($attr_arr) ) {
                $attr_type = wc_get_attribute_taxonomy_names();
                foreach( $attr_type as $attr_slug ) {
                    if( $filter_type != $attr_slug ) {
                        $response['attr_filter'][$attr_slug] = ''; 
                    }
                }
            }else{
                // foreach( $attr_list as $attr_slug => $list ) {
                //     if( $filter_type != $attr_slug ) {
                //         $response['attr_filter'][$attr_slug] = $list; 
                //     }
                // }
                $attr_type = wc_get_attribute_taxonomy_names();
                foreach( $attr_type as $attr_slug ) {
                    if( $filter_type != $attr_slug ) {
                        $response['attr_filter'][$attr_slug] = ( isset($attr_list[$attr_slug]) && !empty($attr_list[$attr_slug]) ?  $attr_list[$attr_slug]: '' ); 
                    }
                }
            }

            // echo "<pre>";
            // print_r($response);
            // echo "</pre>";
            // die;

            // if ( $filter_type != 'pa_size' ) {
            //     $response['attr_pa_size'] = $attr_list['pa_size']; 
            // }

        }

        if ( $filter_type == 'cat' ) {
            $response['wpf_url'] = $wpf_url;
        }else{
            $query_string = http_build_query($query_string_arr);
            $query_string = urldecode($query_string);
            if( $query_string != '' ){
                $wpf_url .= '?'.$query_string;
            }
            $response['wpf_url'] = $wpf_url;
        }
        $response['wpf_page_title'] = $wpf_page_title;
           
        echo json_encode( $response );
        wp_die();
    }


    public function get_breadcrumbs($cat_id) {
            $out = '';
            
            ob_start()
            ?>
                <a href="<?php echo home_url(); ?>">Home</a>
                <span class="breadcrumb-separator"> / </span>
                <?php if( !empty($cat_id) ) { ?>
                <?php $parent_arr_ids = get_ancestors($cat_id, 'product_cat', 'taxonomy');
                    if( !empty($parent_arr_ids) ) {
                        $parent_arr_ids = array_reverse($parent_arr_ids); 
                        foreach($parent_arr_ids as $id) {
                            $term = get_term_by( 'id',$id,'product_cat' );
                            $term_link = get_term_link( $id, 'product_cat' );
                            echo '<a href="'.$term_link.'">'.$term->name.'</a>';
                            echo '<span class="breadcrumb-separator"> / </span>';
                        }
                    }
                    $term = get_term_by( 'id', $cat_id, 'product_cat' );
                    echo $term->name;
                ?>
                <?php }else{
                    echo 'Shop';
                } ?>
            <?php
            $out .= ob_get_clean();

            // if ( function_exists( 'woocommerce_breadcrumb' ) ) {
            //     ob_start();
            //     woocommerce_breadcrumb();
            //     $out .= ob_get_clean();
            // }

            return $out;
    }
       
  
    public function fn_remove_default_sorting_storefront() {
       remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
       remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
       remove_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
       remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20); 
    }

    // public function getAttrTaxonomies() {

    //     $attribute_taxonomies = wc_get_attribute_taxonomies();
    //     $attr_arr = array();

    //     foreach($attribute_taxonomies as $attr) {
    //         $attr_arr[$attr->attribute_name]  =  wc_attribute_taxonomy_name( $attr->attribute_name );
    //     }

    //     return $attr_arr;
    // }

    // public function getTaxonomyTerms($taxonomy) {
    //     $terms = get_terms( $taxonomy, array( 'hide_empty' => '1' ) );
    //     return $terms;
    // }


    public function wpf_attributes_filter_list( $attr_arr=array() ) {
        $out = '';    

        if( empty($attr_arr) ) {
            $attr_arr = $this->get_existing_attr_filter_arr();
        }
        $attr_slug_arr = array();
        ob_start();
        if( !empty($attr_arr) ) {
    
            foreach($attr_arr as $attr_slug => $terms ) {

                $attr_name =  wc_attribute_label($attr_slug);
                if( !empty($terms)):
                    $attr_slug_arr[] = $attr_slug;
                    ?>
                    <div class="wpf_fltr_wrap attr_<?php echo $attr_slug;?>_wrap ">
                        <select class="wpfFilter wpfTaxonomy wpf_fltr" name="<?=$attr_name?>" data-slug="<?=$attr_slug?>" multiple="multiple" data-placeholder="<?=$attr_name?>" >
                            <option value=""><?php echo ucfirst($attr_name)?></option>
                        <?php
                        foreach($terms as $term) {
                           
                            $active_attr = ( isset($_GET[$attr_slug]) && !empty($_GET[$attr_slug]) ) ? explode(',',$_GET[$attr_slug]) : array();

                            ?>
                            <option <?php echo ( in_array( $term->slug, $active_attr ) ) ? 'selected="selected"' : ''; ?> value="<?=$term->slug?>" data-id="<?=$term->term_id?>"><?php echo $term->name ?></option>
                        <?php } ?>
                        </select>
                    </div>
                <?php 
                endif;
            }
        }
        //remaining filters wrapper
        $attrs_raw  = wc_get_attribute_taxonomy_names(); 
        foreach($attrs_raw as $attr_slug) {
            if( !in_array( $attr_slug,$attr_slug_arr  ) ) {
                echo '<div class="wpf_fltr_wrap attr_'. $attr_slug.'_wrap "></div>';
            }
        }

        $out .= ob_get_clean();
        return $out;
    }

    public function get_wpf_attributes_filter_list($attr_arr=array(), $active_attr=array()) {
        $out = [];    

        if( empty($attr_arr) ) {
            $attr_arr = $this->get_existing_attr_filter_arr();
        }
    
        if( !empty($attr_arr) ) {
    
            foreach($attr_arr as $attr_slug => $terms ) {

                $attr_name =  wc_attribute_label($attr_slug);
                if( !empty($terms)):
                    ob_start();
                    ?>
                    <select class="wpfFilter wpfTaxonomy wpf_fltr" name="<?=$attr_name?>" data-slug="<?=$attr_slug?>" multiple="multiple" data-placeholder="<?=$attr_name?>" >
                            <option value=""><?php echo ucfirst($attr_name)?></option>
                        <?php
                        foreach($terms as $term) {
                            
                            // echo "<pre>".$attr_name;
                            // print_r($active_attr[attr_slug]);
                            // echo "</pre>";
                            // die;
                            //$active_attr = ( isset($_GET[$attr_name]) && !empty($_GET[$attr_name]) ) ? $_GET[$attr_name] : '';
                            ?>
                            <option <?php echo ( in_array( $term->slug, $active_attr[$attr_slug] )  ) ? 'selected="selected"' : ''; ?> value="<?=$term->slug?>" data-id="<?=$term->term_id?>"><?php echo $term->name ?></option>
                        <?php } ?>
                    </select>
                    
                <?php 
                    $out[$attr_slug][] .= ob_get_clean();
                endif;
            }
        }
        return $out;
    }  

    // public function get_existing_attr_filter_arr() {


    //     if( !is_shop() && is_product_category() ){

    //         $current_category = get_queried_object();

    //         $filter_raw = array(); 
    //         $attrs_raw  = wc_get_attribute_taxonomy_names(); // Getting data of attributes assign in backend.

    //         $args = array(
    //             'category'  => array( $current_category->slug )
    //         );

    //         foreach( wc_get_products($args) as $product ){
    //             foreach( $product->get_attributes() as $attr_name => $attr ){
    //                 $filter_raw[] = $attr_name;
    //                 if(is_array($attr->get_terms())){    
    //                     foreach( $attr->get_terms() as $term ){
    //                         $terms_raw[] = $term->name;
    //                     }
    //                 }
    //             }
    //         }

    //         $filters = array_unique(array_intersect((array)$filter_raw,(array)$attrs_raw)); //Filtering the attributes used by products in particular category

    //         if(is_array($filters)){    
    //             foreach ( $filters as $filter ){
    //                 $terms = get_terms( $filter );
    //                 if ( ! empty( $terms ) ) {
    //                     foreach ( $terms as $term ) {
    //                         if(in_array($term->name,$terms_raw)) { //Filtering the terms from attribute used by the products in a category and showing required result.

    //                             $return[$filter][] = $term;
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //         return $return;
    //     }else{

    //         $attrs_raw  = wc_get_attribute_taxonomy_names(); // Getting data of attributes assign in backend.
    //         foreach ($attrs_raw as $attr_slug) {
    //             $terms[$attr_slug] = get_terms( $attr_slug, array( 'hide_empty' => '1' ) );
    //         }
    //         return $terms;
    //     }

    // }

    public function get_existing_attr_filter_arr($args=array()) {
        
        $cat = get_queried_object();

        if( ( !is_shop() && is_product_category() ) || !empty($args) || is_a($cat, 'WP_Term') ) {

            if ( !empty($args) ) {
                $args['posts_per_page'] = -1;

            }else{

                if($cat->taxonomy == 'product_cat') {
                    $args = array(
                        'category'  => array( $cat->slug )
                    );
                }else{
                    $args['tax_query'][] = array(
                        'taxonomy'      => $cat->taxonomy,
                        'field'         => 'slug',
                        'terms'         => $cat->slug, 
                        'operator'      => 'IN'
                    );
                }
            }

            $filter_raw = array(); 
            $attrs_raw  = wc_get_attribute_taxonomy_names(); // Getting data of attributes assign in backend.
            $terms_raw = array();
            foreach( wc_get_products($args) as $product ){
                if ( $product->is_type( 'variable' ) ) {

                    foreach ( $product->get_available_variations() as $key ) {
                        $variation = wc_get_product( $key['variation_id'] );

                        $variationName = implode(" / ", $variation->get_variation_attributes());
                        
                        foreach( $variation->get_variation_attributes() as $attr_name => $attr ) {
                            $attr_name = str_replace("attribute_","",$attr_name);
                            
                            $filter_raw[] = $attr_name;
                            $terms_raw[] = $attr;
                            
                         }
                    }

                     
                    
                }else{
                    foreach( $product->get_attributes() as $attr_name => $attr ) {
                        $filter_raw[] = $attr_name;
                        if(is_array($attr->get_terms())) {    
                            foreach( $attr->get_terms() as $term ) {
                                $terms_raw[] = $term->slug;
                            }
                        }
                    }
                }
            }
            
            $filters = array_unique(array_intersect((array)$filter_raw,(array)$attrs_raw)); //Filtering the attributes used by products in particular category
                        if(is_array($filters)) {    
                foreach ( $filters as $filter ) {
                    $terms = get_terms( $filter );
                   

                    if ( ! empty( $terms ) ) {
                        foreach ( $terms as $term ) {
                            if(in_array($term->slug,$terms_raw)) { //Filtering the terms from attribute used by the products in a category and showing required result.

                                $return[$filter][] = $term;
                            }
                        }
                    }
                }
            }
            return $return;
        }else{

            $attrs_raw  = wc_get_attribute_taxonomy_names(); // Getting data of attributes assign in backend.
            foreach ($attrs_raw as $attr_slug) {
                $terms[$attr_slug] = get_terms( $attr_slug, array( 'hide_empty' => '1' ) );
            }
            return $terms;
        }

    }

}


$WooProductFilter = new WooProductFilter();

