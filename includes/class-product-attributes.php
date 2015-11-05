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

class Woocommerce_Bulk_Attribute_Manager_Product_Attibutes {
    public function __construct() {
    }
    
    public function get_list_attributes(){
        return wc_get_attribute_taxonomies();
    }
    
    public function get_list_attribute_slug(){
        $list = $this->get_list_attributes();
        $return = array();
        foreach($list as $item){
            $return[$item->attribute_id]['id'] = $item->attribute_id;
            $return[$item->attribute_id]['tax_slug'] = wc_attribute_taxonomy_name($item->attribute_name);
            $return[$item->attribute_id]['name'] = $item->attribute_name;
            $return[$item->attribute_id]['label'] = $item->attribute_label;
        }
        return $return;
    }
    
    public function get_attribute_select(){
        $return = array();
        $attributes = $this->get_list_attribute_slug();
        foreach($attributes as $attr){
            $label = $attr['label'];
            if($attr['label'] == null){$label = $attr['name'];}
            $select_box = $this->get_attribute_select_box($attr['tax_slug'],$label);
            if(!empty($select_box)){
                $replace = "<select$1 multiple='multiple'>";
                $select_box  = preg_replace( '#<select([^>]*)>#', $replace, $select_box );
                $return[$label] = $select_box;
            }
        }
        return $return;   
    }
    
    public function get_attribute_select_box($term = '',$label = ''){
        
        $args = array(
            //__('Select Any ',WC_BAM_TXT).$label
            'show_option_all'    => '',
            'show_option_none'   => '',
            'option_none_value'  => '-1',
            'orderby'            => 'ID', 
            'order'              => 'ASC',
            'show_count'         => 0,
            'hide_empty'         => 0, 
            'child_of'           => 0,
            'exclude'            => '',
            'echo'               => 0,
            'selected'           => 0,
            'hierarchical'       => 0, 
            'name'               => 'attributes['.$term.'][]',
            'id'                 => $term,
            'class'              => 'wc-enhanced-select attributes_list',
            'depth'              => 0,
            'tab_index'          => 0,
            'taxonomy'           => $term,
            'hide_if_empty'      => true,
            'value_field'	     => 'term_id',	
        );     
        
        return wp_dropdown_categories( $args );
    }
}


?>