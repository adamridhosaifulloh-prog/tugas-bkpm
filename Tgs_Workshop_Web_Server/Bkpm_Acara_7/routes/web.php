<?php

$routes = [

    'GET' => [

        '/' => [
            'controller' => 'AuthController',
            'method' => 'loginForm'
        ],

        '/login' => [
            'controller' => 'AuthController',
            'method' => 'loginForm'
        ],

        '/dashboard' => [
            'controller' => 'HomeController',
            'method' => 'dashboard',
            'middleware' => 'AuthMiddleware'
        ],

        '/mahasiswa' => [
            'controller' => 'MahasiswaController',
            'method' => 'index',
            'middleware' => 'AuthMiddleware'
        ],

        '/mahasiswa/create' => [
            'controller' => 'MahasiswaController',
            'method' => 'create',
            'middleware' => 'AuthMiddleware'
        ],

        '/mahasiswa/edit' => [
            'controller' => 'MahasiswaController',
            'method' => 'edit',
            'middleware' => 'AuthMiddleware'
        ]
    ],

    'POST' => [

        '/login' => [
            'controller' => 'AuthController',
            'method' => 'login'
        ],

        '/logout' => [
            'controller' => 'AuthController',
            'method' => 'logout'
        ]
    ]
];