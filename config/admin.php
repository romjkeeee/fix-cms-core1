<?php

use AltSolution\Admin\Modules;

return [
    'show_favicon' => false,
    'show_footer' => true,
    'site_name' => 'Control Panel',
    'admin_url' => 'admin',

    'items_per_page' => 20,

    'modules' => [
        'admin.module.ribbon' => Modules\RibbonModule::class,
        'admin.module.core' => Modules\CoreModule::class,
        'admin.module.user' =>  Modules\UserModule::class,
        'admin.module.menu' => Modules\MenuModule::class,
        'admin.module.content' => Modules\ContentModule::class,
        'admin.module.option' => Modules\OptionModule::class,
        'admin.module.log' => Modules\LogModule::class,
        'admin.module.email_template' => Modules\EmailTemplateModule::class,
        'admin.module.home_review' => Modules\HomeReviewModule::class,
    ],
    'modules_disabled' => [
        //
    ],

    'logs_lines_per_page' => 1000,
    'logs_lines_preview' => 100,
//    'logs_lines_order' => 'asc',
    'logs_dirs' => [
        'app' => storage_path() . '/logs'
    ],
    'logs_extensions' => ['log'],

    'auth_2fa' => false,
    'auth_2fa_google' => false,

    'elfinder_roots' => [
        'admin::core.el_home' => 'uploads/wysiwyg',
    ],
    //'elfinder_trash' => true,

    'option_cache_ttl' => 2 * 60,

    'upload_directory' => 'uploads',
    'upload_url' => '/uploads',
    'upload_permission' => 0777,
    'upload_keep_source' => true,
    'upload_double_encode' => false,
    'upload_slice_upload' => true,
    'upload_slice_verbose' => false,
    'upload_quality' => 90,

    'field_head_grid' => 'col-sm-2 col-md-2 col-lg-2',
    'field_body_grid' => 'col-sm-10 col-md-10 col-lg-10',
];