<?php
/**
 * Plugin Name:       Woocommerce Bulk Attribute Manager
 * Plugin URI:        https://wordpress.org/plugins/Woocommerce Bulk Attribute Manager/
 * Description:       Manage bulk woocommerce product variations and attribute options
 * Version:           2.2.1
 * Author:            Varun Sridharan
 * Author URI:        http://varunsridharan.in
 * Text Domain:       woocommerce-bulk-attribute-manager
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt 
 * GitHub Plugin URI: https://import.github.com/technofreaky/woocommerce-bulk-attribute-manager/
 */

if ( ! defined( 'WPINC' ) ) { die; }
 
class Woocommerce_Bulk_Attribute_Manager {
	/**
	 * @var string
	 */
	public $version = '2.2.1';
    public static $products = null;
    public static $attribtues = null;

    protected static $_instance = null;
    protected static $functions = null;

    /**
     * Creates or returns an instance of this class.
     */
    public static function get_instance() {
        if ( null == self::$_instance ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    /**
     * Class Constructor
     */
    public function __construct() {
        register_activation_hook( __FILE__, array(__CLASS__,'plugin_activate' ));
        
        if($this->is_request('admin')){
            $this->define_constant();
            $this->load_required_files();
            $this->init_class();
            
            add_action( 'admin_init', array($this,'plugin_activate_redirect' ));
            add_action('admin_menu', array($this,'register_menu'));
            add_action( 'init', array( $this, 'init' ));
        }
    }
    
    public static function plugin_activate() {
        set_transient( 'wc_bam_welcome_screen_activation_redirect', true, 30 );
    }    
    
    public function plugin_activate_redirect() {
        if ( ! get_transient( 'wc_rbp_welcome_screen_activation_redirect' ) ) { return; }
        delete_transient( 'wc_rbp_welcome_screen_activation_redirect' );
        if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) { return; }
        $args = array( 'post_type' => 'product','page' => 'wc-bam-page','section'=>'newsletter' );
        wp_safe_redirect( add_query_arg( $args , admin_url( 'edit.php' ) ) );
    }
    
    public function register_menu(){
        add_submenu_page( 
              'edit.php?post_type=product'   //or 'options.php' 
            , __('Bulk Attribute Manager',WC_BAM_TXT)
            , __('Bulk Attribute Manager',WC_BAM_TXT)
            , 'manage_woocommerce'
            , 'wc-bam-page'
            , array($this,'wc_bam_page')
        );
    }
    
    /**
     * Triggers When INIT Action Called
     */
    public function init(){
        add_action('plugins_loaded', array( $this, 'after_plugins_loaded' ));
        add_filter('load_textdomain_mofile',  array( $this, 'load_plugin_mo_files' ), 10, 2);
    }
    
    /**
     * Loads Required Plugins For Plugin
     */
    private function load_required_files(){
        $this->load_files(WC_BAM_PATH.'includes/common-class-plugin-*');
        $this->load_files(WC_BAM_PATH.'includes/class-*');
    }
    
    /**
     * Inits loaded Class
     */
    private function init_class(){
        self::$functions = new Woocommerce_Bulk_Attribute_Manager_Functions;
        self::$attribtues = new Woocommerce_Bulk_Attribute_Manager_Product_Attibutes;
        self::$products = new Woocommerce_Bulk_Attribute_Manager_Product_Functions;
        
    }
    
    public function wc_bam_page(){
        $page = 'views/page.php';
        if(isset($_REQUEST['section'])){
            $page = 'views/newsletter.php';
        }
        
        $this->load_files(WC_BAM_PATH.$page);
    }
    
    protected function func(){
        return self::$functions;
    }
    
    public function prod(){
        return self::$products;
    }
    
    public function attr(){
        return self::$attribtues;
    }

    protected function load_files($path,$type = 'require'){
        foreach( glob( $path ) as $files ){

            if($type == 'require'){
                require_once( $files );
            } else if($type == 'include'){
                include_once( $files );
            }
            
        } 
    }
    
    /**
     * Set Plugin Text Domain
     */
    public function after_plugins_loaded(){
        load_plugin_textdomain(WC_BAM_TXT, false, WC_BAM_LANGUAGE_PATH );
    }
    
    /**
     * load translated mo file based on wp settings
     */
    public function load_plugin_mo_files($mofile, $domain) {
        if (WC_BAM_TXT === $domain)
            return WC_BAM_LANGUAGE_PATH.'/'.get_locale().'.mo';

        return $mofile;
    }
    
    /**
     * Define Required Constant
     */
    private function define_constant(){
        $this->define('WC_BAM_V',$this->version);
        $this->define('WC_BAM_NAME','Woocommerce Bulk Attribute Manager'); # Plugin Name
        $this->define('WC_BAM_SLUG','wc-bam'); # Plugin Slug
        $this->define('WC_BAM_PATH',plugin_dir_path( __FILE__ )); # Plugin DIR
        $this->define('WC_BAM_LANGUAGE_PATH',WC_BAM_PATH.'languages');
        $this->define('WC_BAM_TXT','Woocommerce-bulk-attribute-manager'); #plugin lang Domain
        $this->define('WC_BAM_URL',plugins_url('', __FILE__ )); 
        $this->define('WC_BAM_ASSET',WC_BAM_URL.'/includes/asset/'); 
        $this->define('WC_BAM_FILE',plugin_basename( __FILE__ ));
    }
    
    /**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
    protected function define($key,$value){
        if(!defined($key)){
            define($key,$value);
        }
    }
    
       

    
	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
    
    
    
}

Woocommerce_Bulk_Attribute_Manager::get_instance();
function WC_BAM(){
    return Woocommerce_Bulk_Attribute_Manager::get_instance();
} 

?>