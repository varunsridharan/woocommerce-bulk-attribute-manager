<?php
/**
 * functionality of the plugin. 
 * @author     Varun Sridharan <varunsridharan23@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) { die; }

class Woocommerce_Bulk_Attribute_Manager_Product_Ajax_Attibutes_Process {
    
    
    public function __construct(){
        add_action( 'wp_ajax_wc_bam_save', array($this,'process' ));
        add_action( 'wp_ajax_wc_bam_check_status', array($this,'check_status' ));
    }
    
    public function check_status(){
        $return = array();
        $status = get_option('wc_bam_status');
        $total =  get_option('wc_bam_total_product');
        $values = get_option('wc_bam_values');
        $count = null;
        if($values['success'] != 0) {
            $count1 = $values['success'] / $total;
            $count2 = $count1 * 100;
            $count = number_format($count2, 0);    
        }
        
        if(empty($values['product_ids'])){$values['product_ids'] = array();}

        $return['status'] = $status;
        $return['total'] = $total;
        $return['completed'] = $values['success'];
        $return['msg'] = sprintf(__('%d of %s Product Attributes Updated ',WC_BAM_TXT),$values['success'],$total);
        $return['width'] = $count ;
        $return['table'] = $this->generate_half_table($values['product_ids']);

        if($return['status'] == 'finished'){
            delete_option('wc_bam_total_product','');
            delete_option('wc_bam_status','');
            delete_option('wc_bam_values','');
        }

        echo json_encode($return);
        exit;
    }
    
                   
    public function process(){
        $output = '';
        delete_option('wc_bam_total_product','');
        delete_option('wc_bam_status','');
        delete_option('wc_bam_values','');
        ini_set('max_execution_time', 3000);
        set_time_limit(0);
        $product_ids = '';
        $total_products = 0;
        if(isset($_POST['product_identity']) && !empty($_POST['product_identity'])){
            
            if($_POST['product_identity'] == 'category'){
                
                $product_ids = $this->get_ids_from_category();
            } else if($_POST['product_identity'] == 'id'){
                $product_ids = $this->get_value_array($_POST['product_ids_skus']);
            } else if($_POST['product_identity'] == 'sku'){
                $productids = $this->get_value_array($_POST['product_ids_skus']);
                $product_ids = WC_BAM()->prod()->get_product_sku($productids);
            }
            
            
            
            $total_products = count($product_ids); 
            update_option('wc_bam_total_product',$total_products);
            update_option('wc_bam_status','running');
            $this->save_products_attribute($product_ids);
            //$output .= $this->generate_table($product_ids);
            update_option('wc_bam_status','finished');
            
        }
        die($output);
    }
    
    public function get_value_array(){
        $comma_seperated = explode(',',$_POST['product_ids_skus'],2);
        if(count($comma_seperated) == 2){
            return explode(',',$_POST['product_ids_skus']);
        } else {
            return explode(PHP_EOL,$_POST['product_ids_skus']);
        }
    }
    
    public function save_products_attribute($product_ids = array()){ 
        $done_ids = array();
        $attribute_update = false;
        $attribute_visibility = 0;
        $attribute_variation = 0;
        $success = 0;
        $post_attributes = @$_POST['attributes'];
        if(isset($_POST['attribute_visibility'])){$attribute_visibility = 1;}
        if(isset($_POST['attribute_update'])){$attribute_update = true;}
        if(isset($_POST['attribute_variation'])){$attribute_variation = 1;}
        if(empty($product_ids)){return false;}
        if(empty($post_attributes)){return false;}
        
        foreach($product_ids as $ids){
            foreach($post_attributes as $attribute_key => $attribute_val){
                $loop_attribute_visibility = $attribute_visibility;
                $loop_attribute_variation = $attribute_variation;
                $attributes = array();
                
                if($attribute_update) { 
                    $values = $this->check_attribtue_exists($ids,$attribute_key);
                    $attr_exist = $values['exist'];
                    $attributes = $values['attr']; 
                }
                
                
                if(isset($attributes[sanitize_title($attribute_key) ]['is_visible']) && 
                   $attributes[sanitize_title($attribute_key) ]['is_visible'] != $loop_attribute_visibility)            {$loop_attribute_visibility = $attributes[sanitize_title($attribute_key) ]['is_visible']; }
                
                if(isset($attributes[sanitize_title($attribute_key) ]['is_visible']) && 
                   $attributes[sanitize_title($attribute_key) ]['is_variation'] != $loop_attribute_variation){$loop_attribute_variation = $attributes[sanitize_title($attribute_key) ]['is_variation']; }
                

                $attributes[ sanitize_title($attribute_key) ] = array(
                    'name'         => wc_clean($attribute_key),
                    'value'        => '',
                    'position'     => 0,
                    'is_visible'   => $loop_attribute_visibility,
                    'is_variation' => $loop_attribute_variation,
                    'is_taxonomy'  => 1
                );            

                $integerIDs = array_map('intval', $attribute_val);
                $response_term = wp_set_object_terms(intval($ids), $integerIDs, $attribute_key,$attribute_update);
                $response = update_post_meta( intval($ids), '_product_attributes', $attributes );
            }        
            $done_ids[] = $ids;
            $success = $success + 1;
            $this->status_update($success,$ids,$done_ids);   
        }
    }
    
    
    public function check_attribtue_exists($ids,$name){
        $attributes = get_post_meta($ids, '_product_attributes',true);
        $return = array();
        $return['exist'] = true;
        $return['attr'] = $attributes;
        
        foreach($attributes as $ak => $av){
            if($ak == $name){
                $return['exist'] = true;
            }
        }
        
        return $return ;
    }
    
    public function get_ids_from_category(){
        $return_ids = array();
        foreach($_POST['product_category'] as $category){
            $ids = WC_BAM()->prod()->get_products_ids_category($category);
            $return_ids =  array_merge($ids,$return_ids);
        }
        return $return_ids;
    }
    
    public function status_update($success = 0,$c = 0, $product_ids = 0){
        update_option('wc_bam_values',array('success' => $success,'current_id'=>$c,'product_ids' => $product_ids));
    }
    
    
    public function generate_table($ids = array()){
        $table = '';
        $productids = $ids;
        
        $table .= '<table>';
            $table .= '<thead>';
                $table .= '<tr>';
                    $table .= '<td>'.__('Product ID',WC_BAM_TXT).'</td>';
                    $table .= '<td>'.__('Product Name',WC_BAM_TXT).'</td>';
                    $table .= '<td>'.__('Product SKU',WC_BAM_TXT).'</td>';
                $table .= '</tr>';
            $table .= '</thead>';
        
            $table .= '<tbody>';
                foreach($productids as $id){
                    $table .= '<tr>';
                        $table .= '<td>'.$id.'</td>';
                        $table .= '<td>'.get_the_title($id).'</td>';
                        $table .= '<td>'.get_post_meta($id,'_sku',true).'</td>';
                    $table .= '</tr>';
                }
            $table .= '</tbody>'; 
        $table .= '</table>';
        return $table;
        
    }
    
    public function generate_half_table($ids){
        $table = '';
        foreach($ids as $id){
            $table .= '<tr>';
                $table .= '<td>'.$id.'</td>';
                $table .= '<td>'.get_the_title($id).'</td>';
                $table .= '<td>'.get_post_meta($id,'_sku',true).'</td>';
            $table .= '</tr>';
        }
        return $table;
    }    
    
}    
new Woocommerce_Bulk_Attribute_Manager_Product_Ajax_Attibutes_Process;
?>