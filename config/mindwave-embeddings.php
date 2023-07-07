<?php

return [
    'default' => env('MINDWAVE_EMBEDDINGS', 'openai'),

    'embeddings' => [
        'openai' => [
            'api_key' => env('MINDWAVE_OPENAI_API_KEY'),
            'org_id' => env('MINDWAVE_OPENAI_ORG_ID'),
            'model' => 'text-embedding-ada-002',
        ],
    ],
];
