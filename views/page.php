<div class="wrap">
    <h1><?php _e(WC_BAM_NAME,WC_BAM_TXT); ?></h1>
    
    <div class="step_container">
        <div class="stepsForm">
            <form id="wcbam_form" method="post">
                <input type="hidden" name="action" value="wc_bam_save"/>
                <?php require('tabs.php'); ?>
                
                <div class="sf-steps-form sf-radius"> 
                    
                    <ul class="sf-content"> <!-- form step one --> 
                         <li>
                            <div class="sf_columns column_6"> 
                                <select name="product_identity" id="product_identity_type" class="wc-enhanced-select"  data-required="true">
                                    <option value=""><?php _e('Select Product Identity Type',WC_BAM_TXT); ?></option>
                                    <option value="id" data-target="field_product_ids_skus">
                                        <?php _e('By Product ID',WC_BAM_TXT); ?> </option>
                                    <option value="category" data-target="field_product_category">
                                        <?php _e('By Product Category',WC_BAM_TXT); ?></option>
                                    <option value="sku" data-target="field_product_ids_skus">
                                        <?php _e('By Product SKU',WC_BAM_TXT); ?></option>
                                </select>

                            </div> 
                         </li>

                        <li id="field_product_ids_skus" class="step_1_options_hide" style="display:none;" >
                            <div class="sf_columns column_6"> 
                                <textarea name="product_ids_skus"><?php _e('Enter Values by , Separated',WC_BAM_TXT); ?></textarea>
                            </div> 
                         </li>

                        <li id="field_product_category" class="step_1_options_hide" style="display:none;">
                            <div class="sf_columns column_6"> 
                                <?php echo  $this->prod()->get_product_category_select(); ?>
                            </div>
                        </li>
                       
                    </ul>  
                    
                    
                    <ul class="sf-content">
                        <li style="margin-bottom: 1rem;"><div class="sf_columns column_6"> <h3 style="margin:0;"><?php _e('Attributes Settings'); ?></h3></div>
                            
                            <div class="sf_columns column_3"><div class="sf-check"><label><input type="checkbox" value="true" name="attribute_visibility"><span></span><?php _e(' Visible on the product page'); ?></label></div></div>
                            
                            <div class="sf_columns column_3"><div class="sf-check"><label><input type="checkbox" value="true" name="attribute_variation"><span></span> <?php _e('Used for variations'); ?></label></div></div>
                            
                            <div class="sf_columns column_3"><div class="sf-check"><label><input type="checkbox" value="true" name="attribute_update"><span></span> <?php _e('Update Attributes'); ?> </label> 
                                <small style="color:red;font-weight:bold:font-size:13px;"> <?php _e('if checked. it will update with the existing attribtues for the products'); ?></small></div></div>

                            <div class="sf_columns column_6" style="min-height:10px;"> <hr/></div></li >

                        <?php
                            $attributes = $this->attr()->get_attribute_select();
                            if(!empty($attributes)){
                                foreach($attributes as $attribute_id => $attribute){
                                    echo '<li><div class="sf_columns column_6">';
                                    echo '<h3 style="margin-top:0;"> '.$attribute_id.'</h3>';
                                    echo $attribute;
                                    echo '</div></li> ';
                                }
                            }
                        ?>
                        
                    </ul>
                    
                    <ul class="sf-content">
                        <li class="final_msg">
                            <h3 id="ajax_message" class="">
                                <?php _e('Are your sure you want to update attributes for selected products ?',WC_BAM_TXT); ?></h3>
<div class="meter animate"> <span style="width: 50%"><span></span></span> </div>
                            
                            <div class="debug_log">
<h2><?php _e('Attribute Information'); ?></h2>
<table id="attribute_result_log" class="table-light">
<thead><tr><td><?php echo __('Attribute ID',WC_BAM_TXT); ?></td><td><?php echo __('Terms',WC_BAM_TXT); ?></td></tr></thead><tbody></tbody></table>  
                                
                            
<h2><?php _e('Product Information'); ?></h2>
<table id="products_result_log" class="table-light">
<thead><tr><td><?php echo __('ID',WC_BAM_TXT); ?></td><td><?php echo __('Name',WC_BAM_TXT); ?></td><td><?php echo __('SKU',WC_BAM_TXT); ?></td></tr></thead><tbody></tbody></table>                            
</div>
                                 

                        </li>
                    </ul>
                    
                    
                </div>
                
                <div class="sf-steps-navigation sf-align-right">
                	<span id="sf-msg" class="sf-msg-error"></span>
                	<button id="sf-prev" type="button" class="sf-button"><?php _e('Previous',WC_BAM_TXT); ?></button>
                    <button id="sf-next" type="button" class="sf-button"><?php _e('Next',WC_BAM_TXT); ?></button>
                </div>
            </form>
        </div>
    </div>

 

</div>