<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default domain labels
    |--------------------------------------------------------------------------
    |
    | Fallback global label saat konfigurasi spesifik tipe institusi belum ada
    | atau belum diisi.
    |
    */
    'defaults' => [
        'guru_label' => 'Guru',
        'class_guardian_label' => 'Wali Kelas',
        'student_label' => 'Murid',
    ],

    /*
    |--------------------------------------------------------------------------
    | Per institution type labels
    |--------------------------------------------------------------------------
    */
    'institution_types' => [
        'sekolah_umum' => [
            'guru_label' => 'Guru',
            'class_guardian_label' => 'Wali Kelas',
            'student_label' => 'Murid',
        ],
        'pondok' => [
            'guru_label' => 'Ustadz/Pengajar',
            'class_guardian_label' => 'Musyrif Kelas',
            'student_label' => 'Murid',
        ],
        'madrasah' => [
            'guru_label' => 'Ustadz/Pengajar',
            'class_guardian_label' => 'Musyrif Kelas',
            'student_label' => 'Murid',
        ],
        'lainnya' => [
            'guru_label' => 'Guru',
            'class_guardian_label' => 'Wali Kelas',
            'student_label' => 'Murid',
        ],
    ],
];
