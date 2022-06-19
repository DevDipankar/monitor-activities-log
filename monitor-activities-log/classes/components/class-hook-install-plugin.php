<?php
class MPA_Hook_Install_plugin {

    public function installed( $upgrader, $extra ) {

        if ( 'install' === $extra['action'] ) {
            $path = $upgrader->plugin_info();
            if ( ! $path ){
                return;
            }
            $data = get_plugin_data( $upgrader->skin->result['local_destination'] . '/' . $path, true, false );
            /*echo '<pre>';
            print_r($upgrader);
            echo '--break--';
            print_r($path);
            echo '--break2--';
            print_r($data);*/
            $args = array(
                'action_type' => 'installed',
                'plugin' => $upgrader->skin->result['local_destination'] . '/' . $path
            );
            mpa_insert_log($args);
        }
        
		
	}  

}