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

class Woocommerce_Bulk_Attribute_Manager_Functions {

    public function __construct() {
        add_filter( 'woocommerce_screen_ids',array($this,'set_wc_screen_ids'),99);
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ),99);
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_filter( 'plugin_row_meta', array($this, 'plugin_row_links' ), 10, 2 );
    }
    
    /**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() { 
        if(in_array($this->current_screen() , $this->get_screen_ids())) {
            wp_enqueue_style(WC_BAM_SLUG.'_core_style',WC_BAM_ASSET.'css/style.css' , array(), WC_BAM_V, 'all' );  
            wp_enqueue_style(WC_BAM_SLUG.'_core_step',WC_BAM_ASSET.'css/jquery.step.css' , array(), WC_BAM_V, 'all' );  
        }
	}
	
    
    /**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
        if(in_array($this->current_screen() , $this->get_screen_ids())) {
            wp_enqueue_script(WC_BAM_SLUG.'_core_step', WC_BAM_ASSET.'js/jquery.steps.min.js', array('jquery'), WC_BAM_V, false ); 
            wp_enqueue_script(WC_BAM_SLUG.'_core_script', WC_BAM_ASSET.'js/script.js', array(WC_BAM_SLUG.'_core_step'), WC_BAM_V, false ); 
        }
        
 
	}
    
    /**
     * Gets Current Screen ID from wordpress
     * @return string [Current Screen ID]
     */
    public function current_screen(){
       $screen =  get_current_screen();
       return $screen->id;
    }
    
    /**
     * Returns Predefined Screen IDS
     * @return [Array] 
     */
    public function get_screen_ids(){
        $screen_ids = array();
        $screen_ids[] = 'product_page_wc-bam-page';
        return $screen_ids;
    }
    
    
    public function set_wc_screen_ids($screens){
        $screen = $screens; 
        $screen[] = 'product_page_wc-bam-page';
        return $screen;
    } 
    
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
	public function plugin_row_links( $plugin_meta, $plugin_file ) {
		if ( WC_BAM_FILE == $plugin_file ) {
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', admin_url('edit.php?post_type=product&page=wc-bam-page'), __('Settings',WC_BAM_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://wordpress.org/plugins/woocommerce-bulk-attribute-manager/faq/', __('F.A.Q',WC_BAM_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://github.com/technofreaky/woocommerce-bulk-attribute-manager', __('View On Github',WC_BAM_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'https://github.com/technofreaky/woocommerce-bulk-attribute-manager/issues/', __('Report Issue',WC_BAM_TXT) );
            $plugin_meta[] = sprintf('&hearts; <a href="%s">%s</a>', 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X9CJPMQSLJGA6', __('Donate',WC_BAM_TXT) );
            $plugin_meta[] = sprintf('<a href="%s">%s</a>', 'http://varunsridharan.in/plugin-support/', __('Contact Author',WC_BAM_TXT) );
		}
		return $plugin_meta;
	}	      
    
}
