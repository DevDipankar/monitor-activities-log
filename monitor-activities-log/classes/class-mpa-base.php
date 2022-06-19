<?php
final class MPA_Base{

    private static $_instance = null;
    public $settings;
    private $current_log_id;
    public $post_type;
    public $action_types;

    function __construct() {
        ob_start();
		global $wpdb;

		$this->settings  = get_option('home');
        $this->action_types = array(
            'installed' => 'Plugin Installed',
            'updated' => 'Plugin Updated',
            //'deleted' => 'Plugin Deleted',
            'activated' => 'Plugin Activated',
            'deactivated' => 'Plugin Deactivated',
            //'file_modified' => 'File Modified'
        );
        $this->post_type = 'mpa_log';
	}

    /**
	 * Get real address
	 * 
	 * @since 2.1.4
	 * 
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

    public function insert( $args ) {
		global $wpdb;
        $abs_path = $args['action_type'] == 'installed' ? false : true ;
        $plugin = $args['action_type'] == 'installed' ? $this->path_split($args['plugin']) : $args['plugin'];
        $_plugin_data = $this->get_plugin_data( $args['plugin'] , $abs_path );

        $time = current_time( 'Y-m-d H:i:s' );

		$args = wp_parse_args(
			$args,
			array(
				'action_time'      => $time,
                'action_ip'        => $this->_get_ip_address(),
                'plugin_name'      => $_plugin_data['Name'],
                'plugin_version'   => $_plugin_data['Version'],
                'plugin_author'   => $_plugin_data['Author'],
                'plugin_description'   => $_plugin_data['Description'],
                'plugin_uri'   => $_plugin_data['PluginURI'],
                'author_uri'   => $_plugin_data['AuthorURI'],
                'title'   => $_plugin_data['Title'],
                'author_name'   => $_plugin_data['AuthorName'],
                'action_type'       => '',
                'plugin' => '',
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
            'post_type' => $this->post_type,
            'post-name' => '',
            'post_date' => $time
          );
           
        // Insert the post into the database
        $mpa_log_id = wp_insert_post( $mpa_log_args );
        $this->current_log_id = $mpa_log_id;
        foreach( $args as $mkey => $mval){
            $this->insert_meta($mkey,$mval);
        }
           
		do_action( 'mpa_insert_log', $args );
	}

    function insert_meta($meta_key,$meta_value,$exit=false){
        if($this->current_log_id){
            update_post_meta($this->current_log_id , $meta_key , $meta_value );
        }
        if($exit){
            $this->current_log_id = 0;
        }
        
    }

    function get_plugin_data($plugin,$absolute_path=true){
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

    function path_split($relative_path){
        if($relative_path){
            $explode = explode( WP_PLUGIN_DIR . '/' , $relative_path );
            return $explode[1];
        }        
    }

    function log_details($log_id){
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

    public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new MPA_Base();
		return self::$_instance;
	}

}