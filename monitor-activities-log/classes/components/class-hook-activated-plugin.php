<?php
class MPA_Hook_Activated_plugin {

    public function activated( $plugin_name ) {
        $args = array(
            'action_type' => 'activated',
            'plugin' => $plugin_name
        );
		mpa_insert_log($args);
	}  

}