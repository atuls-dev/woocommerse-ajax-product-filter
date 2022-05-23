<?php

class Category_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
                'catfl_widget', // Base ID
                'Category Product Filter', // Name
                array('description' => __('Add Woo Category Filter.')) // Args
        );

        add_action( 'widgets_init', function() {
            register_widget( 'Category_Widget' );
        });

    }

    public $args = array(
        'before_title'  => '<h4 class="Category-Filter">',
        'after_title'   => '</h4>',
        'before_widget' => '<div class="category-filter-widget-wrap">',
        'after_widget'  => '</div></div>'
    );

    public function widget($args, $instance) {
        $cat = get_queried_object();
        if (is_a($cat, 'WP_Term')) {
            if ( $cat->taxonomy == 'pwb-brand' ){
                return;
            }
        }

        if( ( !is_shop() && !is_product_category() ) ){
            return;
        }

        echo $args['before_widget'];
 
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
 
        echo '<div class="textwidget">';
            global $WooProductFilter;
            echo $WooProductFilter->wpf_category_filter();
 
        echo '</div>';
 
        echo $args['after_widget'];
    }

    public function update($new_instance, $old_instance) {

        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    public function form($instance) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
        ?>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }
}

$Category_Widget = new Category_Widget();
/*if (version_compare(PHP_VERSION, '5.6.0') >= 0) {
    add_action('widgets_init', function () {
        register_widget("Category_Widget");
    });
} else {
    add_action('widgets_init', create_function('', 'register_widget( "Category_Widget" );'));
}*/
?>