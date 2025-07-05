<?php
declare(strict_types=1);

return [
    'filesystems' => [
        'disks' => [
            'gdrive' => [
                'driver' => 'google',
                'clientId' => getenv('GOOGLE_DRIVE_CLIENT_ID'),
                'clientSecret' => getenv('GOOGLE_DRIVE_CLIENT_SECRET'),
                'refreshToken' => getenv('GOOGLE_DRIVE_REFRESH_TOKEN'),
                'folderId' => getenv('GOOGLE_DRIVE_FOLDER_ID'),
            ],
        ],
    ]
];