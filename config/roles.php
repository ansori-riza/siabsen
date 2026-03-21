<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Role Labels
    |--------------------------------------------------------------------------
    |
    | Label role yang ditampilkan di aplikasi.
    | Bisa disesuaikan per tipe institusi agar lebih dinamis.
    |
    */
    'labels' => [
        'default' => [
            'super_admin' => 'Super Admin',
            'operator' => 'Operator',
            'pembina' => 'Pembina',
            'pengelola' => 'Pengelola',
            'pimpinan' => 'Pimpinan',
        ],

        'pondok' => [
            'operator' => 'Operator Asrama',
            'pembina' => 'Pembina Santri',
            'pengelola' => 'Pengasuh',
            'pimpinan' => 'Pimpinan Pondok',
        ],

        'madrasah' => [
            'operator' => 'Operator Madrasah',
            'pembina' => 'Pembina Kelas',
            'pengelola' => 'Koordinator',
            'pimpinan' => 'Pimpinan Madrasah',
        ],
    ],
];
