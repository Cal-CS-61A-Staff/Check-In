<?php
/**
 * @package OAuth2 Client
 * @author  advanced STORE GmbH
 * Date:    03.04.14
 */
return array(
    'client'		=> array(
        /*
         * Client-ID of your Application
         */
        'id'		=> 'la-manager',
        /*
         * Client-Secret of your Application
         */
        'secret'	=> env('APP_OAUTH_KEY', 'SomeRandomKey'),
    ),
    /*
     * Scopes for your Application (comma separated)
     */
    'scopes'		=> 'email'
);