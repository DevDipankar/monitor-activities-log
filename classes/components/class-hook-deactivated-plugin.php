<?php
class MPA_Hook_Deactivated_plugin {

    public function deactivated( $plugin_name ) {
        $args = array(
            'action_type' => 'deactivated',
            'plugin' => $plugin_name
        );
		mpa_insert_log($args);
	}  

}