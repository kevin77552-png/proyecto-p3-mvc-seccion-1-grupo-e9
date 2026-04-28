<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Livewire Configuration
    |--------------------------------------------------------------------------
    |
    | Only the `temporary_file_upload.max_upload_size` option is defined
    | here to increase the default Livewire temporary upload limit from
    | 12288 KB (12 MB) to 61440 KB (60 MB). Add other options if needed.
    |
    */

    'temporary_file_upload' => [
        // Max upload size in kilobytes (KB). 61440 KB == 60 MB
        'max_upload_size' => 61440,

        // Explicit validation rules applied to temporary uploads.
        // Livewire will use these when validating incoming upload chunks.
        'rules' => ['max:61440'],

        // Disk to store temporary uploads (null -> default disk)
        'disk' => null,
    ],
];
