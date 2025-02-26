<?php

return [
    '/form' => [
        [
            'type'      => 'GET',
            'handler'   => 'FormController@index',   
        ],
    ],
    '/result' => [
        [
            'type'      => 'POST',
            'handler'   => 'FormController@submit',
        ],    
    ],
];