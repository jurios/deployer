<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ignore Files
    |--------------------------------------------------------------------------
    |
    | Here you can list the paths which will be ignored in the deployment.
    | Files like configuration files which are not related with the application
    | in production can be ignored. Files ignored will make your
    | deployment process faster.
    |
    */
    'ignore' => [],

    /*
    |--------------------------------------------------------------------------
    | Include Files
    |--------------------------------------------------------------------------
    |
    | Here you can list the paths which will be included in the deployment.
    | This is the place where you should add files which are not tracked in git
    | and must be deployed in every deploy.
    |
    | An included file which is ignored will be ignored anyway.
    */
    'include' => [],

    /*
    |--------------------------------------------------------------------------
    | Triggers
    |--------------------------------------------------------------------------
    |
    | A trigger allows you add paths/files to the deploy list when a change has
    | been made in a particular path. For example:
    |
    | 'triggers' => [
    |       'resources/scss/*' => [
    |          'public/css/*',
    |          'public/vendor/css/*'
    |       ]
    | ]
    |
    | The previous trigger will deploy every file in 'public/css/' and in 'public/vendor/css'
    | when a file in 'resources/css/' is modified/added/removed.
    |
    | A file added by a trigger which is ignored at the same time will be ignored anyway.
    */
    'triggers' => [],

    'manager' => [

        /*
        |--------------------------------------------------------------------------
        | Protocol
        |--------------------------------------------------------------------------
        |
        | Here you choose which protocol Deployer is going to use. At this moment,
        | you can choose "simulate", "sftp" of "ftp" string. Then you should fill in
        | the respective configuration.
        */
        'protocol' => 'simulate',

        /*
        |--------------------------------------------------------------------------
        | FTP Configuration
        |--------------------------------------------------------------------------
        |
        | FOR SECURITY REASONS, USING ENVIRONMENT VARIABLES IS RECOMMENDED.
        */
        'ftp' => [
            'host' => null,
            'port' => null,
            'user' => null,
            'password' => null,
            'path' => null
        ],

        /*
        |--------------------------------------------------------------------------
        | SFTP Configuration
        |--------------------------------------------------------------------------
        |
        | FOR SECURITY REASONS, USING ENVIRONMENT VARIABLES IS RECOMMENDED.
        */
        'sftp' => [
            'host' => null,
            'port' => null,
            'user' => null,
            'password' => null,
            'path' => null
        ],

        /*
        |--------------------------------------------------------------------------
        | Simulate Configuration
        |--------------------------------------------------------------------------
        */
        'simulate' => [],
    ]
];