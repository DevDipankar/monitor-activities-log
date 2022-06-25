<?php
/**
 * @since 1.0.0
 * 
 * @package Monitor_Plugins_Activities
 * @subpackage Monitor_Plugins_Activities/classes
 * 
 * Base class will handle all major operations
 */
final class MPA_Base{

    /**
     * @since 1.0.0
     * @access  private
     * @var object
     * An instance of this class, 
     */
    private static $_instance = null;

    /**
     * @since 1.0.0
     * @access  public
     * @var string
     * Hold the home url
     */
    public $settings;

    /**
     * @since 1.0.0
     * @access  private
     * @var int
     * Hold Currently loggedin user id
     */
    private $current_log_id;

    /**
     * @since 1.0.0
     * @access  public
     * @var string
     * Post type slug, where all log will be saved, 
     */
    public $post_type;

    /**
     * @since 1.0.0
     * @access  public
     * @var array
     * Definition -  set of all plugin actions 
     */
    public $action_types;

    /**
     * @since 1.0.0
     * @access  public
     * @param void
     * 
     */
    function __construct() {
        ob_start();
		global $wpdb;
		$this->settings  = get_option('home');
        $this->action_types = array(
            'installed'         => 'Plugin Installed',
            'updated'           => 'Plugin Updated',
            'activated'         => 'Plugin Activated',
            'deactivated'       => 'Plugin Deactivated',
            //'file_modified'   => 'File Modified'
            //'deleted'         => 'Plugin Deleted',
        );
        $this->post_type = 'mpa_log';
	}

    /**
	 * Get real address
	 * @since 1.0.0
     * @access  protected
     * @param void
	 * @return string real address IP
	 */
	protected function _get_ip_address() {
		$server_ip_keys = array(
			'HTTP_CF_CONNECTING_IP', // CloudFlare
			'HTTP_TRUE_CLIENT_IP', // CloudFlare Enterprise header
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);
		
		foreach ( $server_ip_keys as $key ) {
			if ( isset( $_SERVER[ $key ] ) && filter_var( $_SERVER[ $key ], FILTER_VALIDATE_IP ) ) {
				return $_SERVER[ $key ];
			}
		}
		// Fallback local ip.
		return '127.0.0.1';
	}


    /**
	 * Insert activities log into database $wpdb->post Table
	 * @since 1.0.0
     * @access  public
     * @param array     $args   The set of data that is required to create logs
     * 
	 * @return int $mpa_log_id   Last created LOG ID
	 */
    public function insert( $args ) {
		global $wpdb;
        $abs_path = $args['action_type'] == 'installed' ? false : true ;
        $plugin = $args['action_type'] == 'installed' ? $this->path_split($args['plugin']) : $args['plugin'];
        $_plugin_data = $this->get_plugin_data( $args['plugin'] , $abs_path );

        $time = current_time( 'Y-m-d H:i:s' );

		$args = wp_parse_args(
			$args,
                array(
                    'action_time'           => $time,
                    'action_ip'             => $this->_get_ip_address(),
                    'plugin_name'           => $_plugin_data['Name'],
                    'plugin_version'        => $_plugin_data['Version'],
                    'plugin_author'         => $_plugin_data['Author'],
                    'plugin_description'    => $_plugin_data['Description'],
                    'plugin_uri'            => $_plugin_data['PluginURI'],
                    'author_uri'            => $_plugin_data['AuthorURI'],
                    'title'                 => $_plugin_data['Title'],
                    'author_name'           => $_plugin_data['AuthorName'],
                    'action_type'           => '',
                    'plugin'                => '',
                )
		);
        $args['plugin'] = $plugin;

		$user = get_user_by( 'id', get_current_user_id() );
		if ( $user ) {
			$args['user_caps'] = strtolower( key( $user->caps ) );
			if ( empty( $args['user_id'] ) )
				$args['user_id'] = $user->ID;
		} else {
			$args['user_caps'] = 'guest';
			if ( empty( $args['user_id'] ) )
				$args['user_id'] = 0;
		}

        $mpa_log_args = array(
            'post_title'    => $args['action_type'],
            'post_content'  => $this->action_types[$args['action_type']],
            'post_status'   => 'inherit',
            'post_author'   => $args['user_id'],
            'post_type'     => $this->post_type,
            'post-name'     => '',
            'post_date'     => $time
          );
           
        // Insert the post into the database
        $mpa_log_id = wp_insert_post( $mpa_log_args );
        $this->current_log_id = $mpa_log_id;
        foreach( $args as $mkey => $mval){
            $this->insert_meta($mkey,$mval);
        }      
		do_action( 'mpa_insert_log', $args );
        return $mpa_log_id;
	}


    /**
	 * Insert meta data for log - into $wpdb->postmeta Table
	 * @since 1.0.0
     * @access  public
     * 
     * @param string    $meta_key      The name of the meta key
     * @param string    $meta_value    The value of the meta
     * @param bool $exit  if true - current log id will be reset into a properties ($this->current_log_id)
     * 
	 * @return void
	 */
    public function insert_meta($meta_key,$meta_value,$exit=false){
        if($this->current_log_id){
            update_post_meta($this->current_log_id , $meta_key , $meta_value );
        }
        if($exit){
            $this->current_log_id = 0;
        }
    }


    /**
	 * Get plugin data such as Plugin Name, Plugin author, plugin Version etc from .readme file
	 * @since 1.0.0
     * @access  public
     * @param string        $plugin                The name/absPath of the respective plugin.
     * @param bool          $absolute_path         IF TRUE - the absolute path of the home directory will be concated with the relative path of the plugin.
     * 
	 * @return object plugin data
	 */
    public function get_plugin_data($plugin,$absolute_path=true){
        if(empty($plugin)){
            return false;
        }
        if($absolute_path){
            return get_plugin_data( 
                WP_PLUGIN_DIR . '/' . $plugin 
            );
        }
        return get_plugin_data(  $plugin );   
    }


    /**
	 * Split and retrive the relative path from a absolute path of a plugin.
	 * @since 1.0.0
     * @access  public
     * @param string        $abs_path                The absolute path of the respective plugin.
     * 
	 * @return string Relative path
	 */
    public function path_split($abs_path){
        if($abs_path){
            $explode = explode( WP_PLUGIN_DIR . '/' , $abs_path );
            return $explode[1];
        }        
    }


    /**
	 * Retrive post data and post meta data of logs (posts)
	 * @since 1.0.0
     * @access  public
     * @param int        $log_id                Post ID of the respective LOG
     * 
	 * @return array Post and PostMeta Data
	 */
    public function log_details($log_id){
        //print_r(get_plugins());die;
        $metaset = array(
            'action_time',
            'action_ip',
            'plugin_name',
            'plugin_version',
            'plugin_author',
            'plugin_description',
            'plugin_uri',
            'author_uri',
            'title',
            'author_name',
            'action_type',
            'plugin',
            'user_caps',
            'user_id'
        );
        foreach($metaset as $meta_key){
            $meta[$meta_key] = get_post_meta( $log_id , $meta_key , true );
        }
        return array(
            'post' => get_post($log_id),
            'meta' => $meta
        );
    }


    /**
	 * Any method or properties of this class, can be called throug this instatnce
	 * @since 1.0.0
     * @access  public
     * @param void
     * 
     * @return self::$_instance     Instance of this class
     * 
	 * @return array Post and PostMeta Data
	 */
    public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new MPA_Base();
		return self::$_instance;
	}

}