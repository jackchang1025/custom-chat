<?php

return [
    'default' => env('MINDWAVE_VECTORSTORE', 'pinecone'),

    'vectorstores' => [
        'array' => [],

        'file' => [
            'path' => env('MINDWAVE_VECTORSTORE_PATH', storage_path('mindwave/vectorstore.json')),
        ],

        'pinecone' => [
            'api_key' => env('MINDWAVE_PINECONE_API_KEY'),
            'environment' => env('MINDWAVE_PINECONE_ENVIRONMENT'),
            'index' => env('MINDWAVE_PINECONE_INDEX_NAME','items'),
        ],

        'weaviate' => [
            'api_url' => env('MINDWAVE_WEAVIATE_URL'),
            'api_token' => env('MINDWAVE_WEAVIATE_API_TOKEN'),
            'additional_headers' => [],
            'index' => 'items',
        ],

        'qdrant' => [
            'host' => env('MINDWAVE_QDRANT_HOST', 'localhost'),
            'port' => env('MINDWAVE_QDRANT_PORT', '6333'),
            'api_key' => env('MINDWAVE_QDRANT_API_KEY', ''),
            'collection' => env('MINDWAVE_QDRANT_COLLECTION', 'items'),
        ],
    ],
];
