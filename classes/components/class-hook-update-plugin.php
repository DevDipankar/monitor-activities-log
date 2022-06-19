<?php
class MPA_Hook_Update_plugin {

    public function updated( $upgrader, $extra ) {

        if ( 'update' === $extra['action'] ) {

            if ( isset( $extra['bulk'] ) && true == $extra['bulk'] ) {
				$slugs = $extra['plugins'];
			} else {
				$plugin_slug = isset( $upgrader->skin->plugin ) ? $upgrader->skin->plugin : $extra['plugin'];

				if ( empty( $plugin_slug ) ) {
					return;
				}

				$slugs = array( $plugin_slug );
			}
			
			foreach ( $slugs as $slug ) {

               // $data = MPA_Base::instance()->get_plugin_data( $slug );

                $args = array(
                    'action_type' => 'updated',
                    'plugin' => $slug
                );
                mpa_insert_log($args);

			}
     
        }		
	}  //updated function end

 

}