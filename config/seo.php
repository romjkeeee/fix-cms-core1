<?php

return [
    'separator' => ' – ',
    'default_fields' => [
        'title' => [
            'label' => 'admin::seo.f_title',
            'type' => 'title',
            'edit' => true,
        ],
        'keywords' => [
            'label' => 'admin::seo.f_keywords',
            'type' => 'meta-name',
            'edit' => true,
        ],
        'description' => [
            'label' => 'admin::seo.f_description',
            'type' => 'meta-name',
            'edit' => true,
        ],
    ],
    'cache_ttl' => 2 * 60,
    'enable_common' => true,
    //'enable_site_name' => true,
];
