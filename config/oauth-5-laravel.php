<?php
return [

    /*
    |--------------------------------------------------------------------------
    | oAuth Config
    |--------------------------------------------------------------------------
    */

    /**
     * Storage
     */
    'storage' => 'Session',

    /**
     * Consumers
     */
    'consumers' => [

        /**
         * OK
         */
        'ok' => [
            'client_id'     => 'la-manager',
            'client_secret' => env('CoFR9qnNn1ohISWAPsVA0tnHtpVue9O', 'SomeRandomKey'),
            'scope'         => ['email'],
        ],

    ]

];