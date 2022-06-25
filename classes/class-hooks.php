<?php
/**
 * @since 1.0.0
 * 
 * @package Monitor_Plugins_Activities
 * @subpackage Monitor_Plugins_Activities/classes
 * 
 * Set all action/filter hooks of each plugin activities
 * 
 */
class MPA_Hooks{

    protected $actions;
    protected $filters;

    function __construct(){

        //add your custom hooks here
        ##
	    #* @hook             The name of the WordPress filter that is being registered.
	    #* @component        A reference to the instance of the object on which the filter is defined.
	    #* @callback         The name of the function definition on the $component.
	    #* @priority         The priority at which the function should be fired.
	    #* @accepted_args    The number of arguments that should be passed to the $callback.
        ##

        $this->actions = array(
            array(
                'hook' => 'activated_plugin',
                'component' => 'MPA_Hook_Activated_plugin',
                'callback' => 'activated',
                'priority' => '90',
                'accepted_args' => 2
            ),
            array(
                'hook' => 'deactivated_plugin',
                'component' => 'MPA_Hook_Deactivated_plugin',
                'callback' => 'deactivated',
                'priority' => '90',
                'accepted_args' => 2
            ),
            array(
                'hook' => 'upgrader_process_complete',
                'component' => 'MPA_Hook_Install_plugin',
                'callback' => 'installed',
                'priority' => '10',
                'accepted_args' => 2
            ),
            array(
                'hook' => 'upgrader_process_complete',
                'component' => 'MPA_Hook_Update_plugin',
                'callback' => 'updated',
                'priority' => '10',
                'accepted_args' => 2
            ),
            /*array(
                'hook' => 'wp_redirect',
                'component' => 'MPA_Hook_Update_plugin',
                'callback' => 'wp_redirect',
                'priority' => '10',
                'accepted_args' => 2
            ),*/
           /* array(
                'hook' => 'init',
                'component' => 'MPA_Hook_Activated_plugin',
                'callback' => 'test_callba',
                'priority' => '10',
            ),*/
        );

        $this->filters = array();
    }

}