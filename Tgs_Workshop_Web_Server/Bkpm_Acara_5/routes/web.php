<?php

$routes = [

    'GET' => [
        '/' => [
            'controller' => 'HomeController',
            'method' => 'index'
        ],

        '/mahasiswa' => [
            'controller' => 'MahasiswaController',
            'method' => 'index'
        ],

        '/mahasiswa/create' => [
            'controller' => 'MahasiswaController',
            'method' => 'create'
        ],

        // Tugas Mandiri: /mahasiswa/5
        '/mahasiswa/{id}' => [
            'controller' => 'MahasiswaController',
            'method' => 'show'
        ]
    ]

];