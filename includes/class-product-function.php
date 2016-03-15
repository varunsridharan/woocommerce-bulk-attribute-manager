<?php
/**
 * functionality of the plugin.
 *
 * @link       @TODO
 * @since      1.0
 *
 * @package    @TODO
 * @subpackage @TODO
 *
 * @package    @TODO
 * @subpackage @TODO
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class Woocommerce_Bulk_Attribute_Manager_Product_Functions {
    public function __construct() {
    }
    
    
    public function get_product_sku($skus = array()){
        $return = array();
        foreach($skus as $sku){
            if(empty($sku)){continue;}
            $return[] = wc_get_product_id_by_sku($sku);
        }
        return $return;
        
    }
    public function get_product_category_select(){
        
        $args = array(
            'show_option_all'    => '',
            'show_option_none'   => '',
            'option_none_value'  => '-1',
            'orderby'            => 'ID', 
            'order'              => 'ASC',
            'show_count'         => 0,
            'hide_empty'         => 1, 
            'child_of'           => 0,
            'exclude'            => '',
            'echo'               => 0,
            'selected'           => 0,
            'hierarchical'       => 0, 
            'name'               => 'product_category',
            'id'                 => '',
            'class'              => 'wc-enhanced-select',
            'depth'              => 0,
            'tab_index'          => 0,
            'taxonomy'           => 'product_cat',
            'hide_if_empty'      => false,
            'value_field'	     => 'term_id',	
        );     
        
        return wp_dropdown_categories( $args );
    }
    
    public function get_products_ids_category($category_id){
        $args = array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'ignore_sticky_posts'   => 1,
        'posts_per_page'        => -1,
        'fields' => 'ids',
        'tax_query'             => array(
                                        array(
                                        'taxonomy'      => 'product_cat', 
                                        'terms'         => $category_id,
                                        'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                                        )
                                    )
        );
        $products = new WP_Query($args);
        return $products->get_posts();
    }
    
}

?>