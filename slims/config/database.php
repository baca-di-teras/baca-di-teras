<?php

return [
    /**
     * Default database node profile
     */
    'default_profile' => 'SLiMS',

    /**
     * SLiMS as Service, One SLiMS for many library
     * ----------------------------------------------------
     * 
     * Switching database node access based on rule,
     * such as domain, ip, port etc
     * 
     * How to :
     * 1. make file with name database_proxy.php in config/
     * 2. make your own rule in that file.
     * 3. change this value to true
     */
    'proxy' => false,

    /**
     * Nodes profile
     */
    'nodes' => [
        'SLiMS' => [
            'host' => 'localhost',
            'database' => 'bacadite_bacaditeras_slims',
            'port' => 3306,
            'username' => 'bacadite_bacaditeras_slims',
            'password' => 'UHxCYcVpnXy2SUqSyUGW',
            'options' => [
                'storage_engine' => 'InnoDB'
            ]
        ],
        // add more here
    ]
];