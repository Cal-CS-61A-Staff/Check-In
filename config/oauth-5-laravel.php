<?php

use OAuth\Common\Storage\Session;

return [

    /*
    |--------------------------------------------------------------------------
    | oAuth Config
    |--------------------------------------------------------------------------
    */

    /**
     * Storage
     */
    'storage' => new Session(),

    /**
     * Consumers
     */
    'consumers' => [

        /**
         * OK
         */
        'Ok' => [
            'client_id'     => 'la-manager',
            'client_secret' => env('CoFR9qnNn1ohISWAPsVA0tnHtpVue9O', 'SomeRandomKey'),
            'scope'         => ['email'],
        ],

    ]

];