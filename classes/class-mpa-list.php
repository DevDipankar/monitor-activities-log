<?php
/**
 * In this part you are going to define custom table list class,
 * that will display your database records in nice looking table
 * http://codex.wordpress.org/Class_Reference/WP_List_Table
 * http://wordpress.org/extend/plugins/custom-list-table-example/
 */
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
/**
 * 
 * records in nice table
 */
class MPA_List_Log_Table extends WP_List_Table
{

    public $plugin_list;
    //[REQUIRED] You must declare constructor and give some basic params
    function __construct()
    {
        global $status, $page;
        parent::__construct(array(
		
            'singular' => 'mpa_main_menu',
            'plural' => 'mpa_main_menu',
        ));
        //add_action( 'wp_enqueue_scripts', 'mm_theme_add_css_js' );
        $this->plugin_list = get_plugins();
    }

    function filter_filters(){
        $labels = array(
            'mpa_fil_plugin' => 'Plugins',
            'mpa_fil_users' => 'Users',
            'mpa_fil_plugin_status' => 'Plugin Status',
            'mpa_fil_user_caps' => 'User Caps',
            'mpa_fil_date' => 'Date'
        );
        $html = ob_start();
        if(
            !empty($_REQUEST['mpa_fil_plugin']) ||
            !empty($_REQUEST['mpa_fil_users']) ||
            !empty($_REQUEST['mpa_fil_plugin_status']) ||
            !empty($_REQUEST['mpa_fil_user_caps']) ||
            !empty($_REQUEST['mpa_fil_date']) 
        ){

            $log_daterange = $_REQUEST['mpa_start_log_date'] ? sanitize_text_field($_REQUEST['mpa_start_log_date']) .'!'. sanitize_text_field($_REQUEST['mpa_end_log_date']) : '';
            $filters = array(
                'mpa_fil_plugin' => sanitize_text_field($_REQUEST['mpa_fil_plugin']),
                'mpa_fil_users' => sanitize_text_field($_REQUEST['mpa_fil_users']),
                'mpa_fil_plugin_status' => sanitize_text_field($_REQUEST['mpa_fil_plugin_status']),
                'mpa_fil_user_caps' => sanitize_text_field($_REQUEST['mpa_fil_user_caps']),
                'mpa_fil_date' => $log_daterange
            );
            ?>
            <div class="mpa_list_filters">
                <span class="h1">Filter(s) Applied:</span>
                <?php
                    foreach( $filters as $fil_key=>$f_val){
                        if($f_val){
                            if($fil_key == 'mpa_fil_date' ){
                                $explode = explode('!',$f_val);
                                $one_date_value = date('F j, Y' , strtotime($explode[0]) );
                                $two_date_value = date('F j, Y' , strtotime($explode[1]) );
                                $fil_value = $one_date_value .' - '. $two_date_value;
                            }elseif( $fil_key == 'mpa_fil_users'){
                                $get_user = get_user_by( 'id' , sanitize_text_field($_REQUEST['mpa_fil_users']) );
                                $fil_value = $get_user->display_name;
                            }elseif( $fil_key == 'mpa_fil_plugin'){
                                $get_plugin_data = $this->plugin_list[ $_REQUEST['mpa_fil_plugin'] ];
                                $fil_value = $get_plugin_data['Name'];
                            }else{
                                $fil_value = $f_val;
                            }
                            echo '<span class="badge bg-dark">'.esc_attr($labels[$fil_key]).'</span>';
                            echo '<span class="badge bg-light text-dark me-3 border">'.esc_attr($fil_value).'<span class="close mpa_removefilter" filkey="'.esc_attr($fil_key).'" ><i class="fa fa-times-circle"></i></span></span>';
                        }                   
                    }
                ?>
            </div>
            <?php
        }
        $html = ob_get_clean();
        return $html;
    }

    function extra_tablenav( $which ) {
       
        global $wpdb, $testiURL, $tablename, $tablet, $current_user, $wp_roles;
        if ( $which == "top" ){
            
            ?>
            <div class="alignleft actions bulkactions mpa_log_list_filters">
            <label for="mpa_fil_plugin" class="screen-reader-text">All Plugin</label>
            <select name="mpa_fil_plugin" id="mpa_fil_plugin" style="width:120px;" class="fo-color">
                <option value="">All Plugins</option>
                <?php 
                if(!empty($this->plugin_list)){
                    foreach($this->plugin_list as $plugin_key=>$p_values){
                         $selected = isset($_GET['mpa_fil_plugin']) && sanitize_text_field($_GET['mpa_fil_plugin']) == $plugin_key ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($plugin_key);?>" <?php echo esc_attr($selected); ?>><?php echo esc_attr($p_values['Name']);?></option>

                <?php   
                    }
                } ?>
            </select>
            <label for="mpa_fil_users" class="screen-reader-text">All Users</label>
            <select name="mpa_fil_users" id="mpa_fil_users" style="width:155px;" class="fo-color">
                <option value="">All Users</option>
                <?php 
                $all_users = get_users();
                if(!empty($all_users)){
                    foreach($all_users as $u_key => $u_values){
                        $uid = $u_values->data->ID;
                        $uname = $u_values->data->display_name;
                        $u_selected = isset($_GET['mpa_fil_users']) && sanitize_text_field($_GET['mpa_fil_users']) == $uid ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($uid);?>" <?php echo esc_attr($u_selected); ?>><?php echo esc_attr($uname);?></option>

                <?php   
                    }
                } ?>
            </select>

            <label for="mpa_fil_plugin_status" class="screen-reader-text">All Status</label>
            <select name="mpa_fil_plugin_status" id="mpa_fil_plugin_status" style="width:155px;" class="fo-color">
                <option value="">All Status</option>
                <?php 
                $all_status = MPA_Base::instance()->action_types;
                if(!empty($all_status)){
                    foreach($all_status as $st_key => $st_values){
                        $st_selected = isset($_GET['mpa_fil_plugin_status']) && sanitize_text_field($_GET['mpa_fil_plugin_status']) == $st_key ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($st_key);?>" <?php echo esc_attr($st_selected); ?>><?php echo esc_attr($st_values);?></option>

                <?php   
                    }
                } ?>
            </select>

            <label for="mpa_fil_user_caps" class="screen-reader-text">All Users Caps</label>
            <select name="mpa_fil_user_caps" id="mpa_fil_user_caps" style="width:155px;" class="fo-color">
                <option value="">All Users Caps</option>
                <?php 
                if(!empty($wp_roles)){
                    foreach($wp_roles->roles as $cp_key => $cp_values){
                        $cp_selected = isset($_GET['mpa_fil_user_caps']) && sanitize_text_field($_GET['mpa_fil_user_caps']) == $cp_key ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($cp_key);?>" <?php echo esc_attr($cp_selected); ?>><?php echo esc_attr($cp_values['name']);?></option>

                <?php   
                    }
                } ?>
            </select>
            
            <?php 
            $c_a_r_s_value = '';
            if(!empty($_GET['mpa_start_log_date']) && !empty($_GET['mpa_end_log_date'])){
              $c_a_r_s_value = date('m/d/Y',strtotime(sanitize_text_field($_GET['mpa_start_log_date']))).' - '. date('m/d/Y',strtotime(sanitize_text_field($_GET['mpa_end_log_date'])));
            }
            $start_cr_val = '';
            if(!empty($_GET['mpa_start_log_date'])){
               $start_cr_val = sanitize_text_field($_GET['mpa_start_log_date']); 
            }
            $end_cr_val = '';
            if(!empty($_GET['mpa_end_log_date'])){
               $end_cr_val = sanitize_text_field($_GET['mpa_end_log_date']); 
            }
            ?>

            <div class="iconincfld">
            <label for="mpa_fil_date" class="screen-reader-text">Log date range</label>
                <i class="fa fa-calendar first"></i>
                <input type="text" name="mpa_fil_date" id="mpa_fil_date" placeholder="Log date range" autocomplete="off" value="<?php echo esc_attr($c_a_r_s_value); ?>" class="fo-color" />
                <i class="fa fa-times-circle d_clear second" id="c_req_dt" <?php if($start_cr_val){ echo 'style="display:inline-block;"';}?> ></i>
                <input type="hidden" name="mpa_start_log_date" id="mpa_start_log_date" value="<?php echo esc_attr($start_cr_val); ?>">
                <input type="hidden" name="mpa_end_log_date" id="mpa_end_log_date" value="<?php echo esc_attr($end_cr_val); ?>">
            </div>
    

            <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
            <!-- <input type="button" name="export_action" id="export-query-submit" class="button" value="Export"> -->
            </div>
            <?php
        }
        if ( $which == "bottom" ){
            //The code that goes after the table is there
    
        }
    }

    public function display_tablenav( $which ) {
		if ( 'top' == $which ) {
			$this->search_box( __( 'Search', 'monitor_activities_log' ), 'mpa-search' );
		}
        $this->bulk_actions( $which );
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php
            
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
			<br class="clear" />
		</div>
		<?php
	}

    public function search_box( $text, $input_id ) {
		$search_data = isset( $_REQUEST['mpa_s'] ) ? sanitize_text_field( $_REQUEST['mpa_s'] ) : '';

		$input_id = $input_id . '-search-input';
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo esc_attr($input_id); ?>"><?php echo esc_attr($text); ?>:</label>
			<input type="search" id="<?php echo esc_attr($input_id); ?>" name="mpa_s" value="<?php echo esc_attr( $search_data ); ?>" />
			<?php submit_button( $text, 'button', false, false, array('id' => 'search-submit') ); ?>
		</p>
	<?php
	}

    public function display() {
        $singular = $this->_args['singular'];
        $this->display_tablenav( 'top' );
        $this->screen->render_screen_reader_content( 'heading_list' );
        echo $this->filter_filters();
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
            <thead>
                <tr><?php $this->print_column_headers(); ?></tr>
            </thead>
            <tbody id="the-list"
                <?php
                if ( $singular ) {
                    echo " data-wp-lists='list:".esc_attr($singular)."'";
                }
                ?>
                >
                <?php $this->display_rows_or_placeholder(); ?>
            </tbody>
            <tfoot>
                <tr><?php $this->print_column_headers( false ); ?></tr>
            </tfoot>     
        </table>
        <?php
        $this->display_tablenav( 'bottom' );
    }

    /*
     * [REQUIRED] this is a default column renderer
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
		global $wpdb;
		$table_name = $wpdb->prefix.'posts';
        $details = MPA_Base::instance()->log_details( $item['ID'] );
        //echo '<pre>';print_r($details);die;
		
		switch($column_name) {

            case 'LOG_ID':
                echo '<span class="" >'.esc_attr($item['ID']).'</span>';
            break;

            case 'ip':
                echo esc_attr($details['meta']['action_ip']);
			break;
			
            case 'plugin_name':
                $plugin_data = explode('/' , $details['meta']['plugin'] );
                echo sprintf(
                    '<a href="javascript:;"  data-link="%s" class="mpa_plugin_iframe" aria-label="%s" data-title="%s">%s</a>',
                    esc_url(
                        admin_url(
                            'plugin-install.php?tab=plugin-information&plugin=' . esc_attr($plugin_data[0]) .
                            '&TB_iframe=true&width=900&height=750'
                        )
                    ),
                    /* translators: %s: Plugin name. */
                    esc_attr( sprintf( __( 'More information about %s' ), $details['meta']['plugin_name'] ) ),
                    esc_attr( $details['meta']['plugin_name'] ),
                    __( $details['meta']['plugin_name'] )
                );
                //echo '<a href="javascript:;"  data-link="'.esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_data[0] .'&TB_iframe=true&width=600&height=550' )).'" class="mpa_plugin_iframe" aria-label="'.$details['meta']['plugin_name'].'" data-title="'.sanitize_text_field( $details['meta']['plugin_name'] ).'">View details</a>';
                //echo $details['meta']['plugin_name'];
			break;

            case 'action_type':
                echo esc_attr($details['meta']['action_type']);
            break;

            case 'time':
                $posted = get_the_time('U',$item['ID']);
                echo '<span style="color:green;font-weight:bold;">'.human_time_diff($posted,current_time( 'U' )). " ago</span>";
                echo esc_attr('[ '.$item['post_date'].' ]');
			break;

            case 'message' :
                echo esc_attr($details['post']->post_content);
            break;

            case 'version' :
                echo esc_attr($details['meta']['plugin_version']);
            break;

            case 'author' :
                echo '<a href="'.esc_attr($details['meta']['author_uri']).'" target="_blank" >'.esc_attr($details['meta']['author_name']).'</a>';
            break;

            case 'description' :
                echo esc_attr($details['meta']['plugin_description']);
            break;

            case 'user' :
                global $wp_roles;
                $user_id = $details['meta']['user_id'];
                if ( ! empty( $user_id ) && 0 !== (int) $user_id ) {
                    $user = get_user_by( 'id', $user_id );
                    if ( $user instanceof WP_User && 0 !== $user->ID ) {
                        echo sprintf(
                            '<a href="%s" target="_blank" class="mpa_logbyuser">%s <span class="mpa-author-name">%s</span><br><span class="aal-author-caps">%s</span></a>',
                            admin_url('user-edit.php?user_id='.esc_attr($user->ID)),
                            get_avatar( $user->ID, 35 ),
                            ucfirst($user->display_name),
                            isset( $user->roles[0] ) && isset( $wp_roles->role_names[ $user->roles[0] ] ) ? $wp_roles->role_names[ $user->roles[0] ] : 'Undefined'
                        );
                    }
                }
            break;
            
			
            default:
               return $item[$column_name];
        }        
    }
    /**
     * [OPTIONAL] this is example, how to render specific column
     * method name must be like this: "column_[column_name]"
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_ID($item)
    {
        $actions = array();
        return sprintf('%s %s',
            $item['ID'],
            $this->row_actions($actions)
        );
    }
	
    /**
     * [REQUIRED] this is how checkbox column renders
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['ID']
        );
    }
    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     * @return array
     */
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'LOG_ID' => '#LOGID',
            'plugin_name' => 'Plugin Name',
            'action_type' => 'Action',
            'time' => __('When'),
            'ip' => __('IP'),
            'message' => __('Log Message'),
            'version' => __('Plugin Version'),
            'author' => __('Plugin Author'),
            'description' => __('Description'),
            'user' => __('User'),
			
	    );
        return $columns;
    }
    /*
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'time' => array('time', true),
            'LOG_ID' => array('LOG_ID', true),
        );
        return $sortable_columns;
    }
	
	
	//Use the "manage{$page}columnshidden" option, maintained by WordPress core:
	function get_hidden_columns(){
        $columns =  (array) get_user_option( 'managetoplevel_page_mpa_main_menucolumnshidden' );
       // print_r($columns);die;
        return $columns;
    }
    /*
     * [OPTIONAL] Return array of bult actions if has any
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete',
        );
        return $actions;
    }

    function no_items() {
        _e( 'No Logs found.' );
    }


    /*
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'posts'; // do not forget about tables prefix
        $postmeta = $wpdb->prefix.'postmeta';
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? rest_sanitize_array($_REQUEST['id']) : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE ID IN($ids)");
                $wpdb->query("DELETE FROM $postmeta WHERE post_id IN($ids)");
            }
        }
    }
    /**
     * [REQUIRED] This is the most important method
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {

        global $wpdb,$current_user;
        $table_name = $wpdb->prefix.'posts'; // do not forget about tables prefix
		$per_page = get_user_option('mpa_log_per_page');
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);
		
        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();      
		
        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'ID';
        $order = ( isset($_REQUEST['order']) && sanitize_sql_orderby($_REQUEST['order']) ) ? sanitize_text_field($_REQUEST['order']) : 'desc';

        $per_page = $per_page ? $per_page : 50;

        $post_status = 'inherit';
        $args['post_type'] = MPA_Base::instance()->post_type;
        $args['posts_per_page'] = $per_page;
        $args['post_status'] = $post_status;
        $args['order'] = $order;
        $args['orderby'] = $orderby;
        $args['paged'] = $paged;

        ///filter plugins
        if( !empty($_GET['mpa_fil_plugin']) ){
            $args['meta_query'][] =  array(
                'key'     => 'plugin',
                'value'   => sanitize_text_field($_GET['mpa_fil_plugin']),
                'compare' => '=',
            );
            
        }

        //filter users
        if( !empty($_GET['mpa_fil_users']) ){
            $args['meta_query'][] =  array(
                'key'     => 'user_id',
                'value'   => sanitize_text_field($_GET['mpa_fil_users']),
                'compare' => '=',
            );
            
        }

        //filter plugin status
        if( !empty($_GET['mpa_fil_plugin_status']) ){
            $args['meta_query'][] =  array(
                'key'     => 'action_type',
                'value'   => sanitize_text_field($_GET['mpa_fil_plugin_status']),
                'compare' => '=',
            );
            
        }

        //user caps filter
        if( !empty($_GET['mpa_fil_user_caps']) ){
            $args['meta_query'][] =  array(
                'key'     => 'user_caps',
                'value'   => sanitize_text_field($_GET['mpa_fil_user_caps']),
                'compare' => '=',
            );
            
        }

        //date filter
        if( !empty($_GET['mpa_start_log_date']) &&  !empty($_GET['mpa_end_log_date'])){
            $startdate = date('F jS, Y', strtotime(sanitize_text_field($_GET['mpa_start_log_date'])));
            $enddate = date('F jS, Y', strtotime( sanitize_text_field($_GET['mpa_end_log_date'] . ' +1 day')));
            
            $args['date_query'][] = array(
                'after' =>  $startdate,
                'before' => $enddate,
                'inclusive' => false,
            );
        }

        //search
        if(!empty($_GET['mpa_s'])){
            //$args['s'] = $_GET['mpa_s'];
            $args['meta_query'] =  array(
                'relation' => 'OR',
                array(
                    'key'     => 'action_ip',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ), 
                'relation' => 'OR',
                array(
                    'key'     => 'plugin_name',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ),
                'relation' => 'OR',
                array(
                    'key'     => 'plugin_version',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ),
                'relation' => 'OR',
                array(
                    'key'     => 'plugin_description',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ),
                'relation' => 'OR',
                array(
                    'key'     => 'plugin_uri',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ),
                'relation' => 'OR',
                array(
                    'key'     => 'author_uri',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ),
                'relation' => 'OR',
                array(
                    'key'     => 'title',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ),
                'relation' => 'OR',
                array(
                    'key'     => 'author_name',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ),
                array(
                    'key'     => 'action_type',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ),
                'relation' => 'OR',
                array(
                    'key'     => 'plugin',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ),
                'relation' => 'OR',
                array(
                    'key'     => 'user_caps',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ),
                'relation' => 'OR',
                array(
                    'key'     => 'user_id',
                    'value'   => sanitize_text_field($_GET['mpa_s']),
                    'compare' => 'LIKE',
                ),
            );
        }

        //echo '<pre>';print_r($args);die;

        $postsQ = new WP_Query( $args );
        //echo '<pre>';print_r($postsQ);die;

        $posts = $postsQ->posts;
		$postsItems = json_decode(json_encode($posts), true);
		
        $this->items = $postsItems;
        $total_items = $postsQ->found_posts;
				
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}
/*
 * List page handler
 * This function renders our custom table
 * Notice how we display message about successfull deletion
 * Actualy this is very easy, and you can add as many features
 * as you want.
 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples
 */
?>

