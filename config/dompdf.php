<?php

return [
    'show_warnings'    => false,
    'orientation'      => 'portrait',
    'defines'          => [
        'DOMPDF_UNICODE_ENABLED'  => true,
        'DOMPDF_ENABLE_REMOTE'    => true,
        'FONT_DIR'                => storage_path('fonts/'),
        'FONT_CACHE'              => storage_path('fonts/'),
        'TEMP_DIR'                => sys_get_temp_dir(),
        'DOMPDF_LOG_OUTPUT_FILE'  => storage_path('logs/dompdf.htm'),
        'DOMPDF_DEFAULT_PAPER_SIZE' => 'A4',
        'DOMPDF_DEFAULT_FONT'     => 'sans-serif',
        'DOMPDF_DPI'              => 96,
        'DOMPDF_ENABLE_PHP'       => false,
        'DOMPDF_ENABLE_CSS_FLOAT' => true,
        'DOMPDF_ENABLE_HTML5_PARSER' => true,
    ],
];
