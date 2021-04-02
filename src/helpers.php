<?php

use AltSolution\Admin\Form;
use AltSolution\Admin\EmailTemplate;
use AltSolution\Admin\Helpers\SeoInterface;
use AltSolution\Admin\Option;
use AltSolution\Admin\Seo;
use AltSolution\Admin\System;
use AltSolution\Admin\Upload\UploadManagerInterface;
use AltSolution\Admin\Image\ImageManagerInterface;
use Illuminate\Http\UploadedFile;
use Psr\Log\LoggerInterface;

/*
 * Helpers naming:
 * . one action: ns-verb-noun
 * . . cms_send_template()
 * . many actions: ns-noun
 * . . cms_seo()
 *
 */

/**
 * @return System\System
 */
function cms_system()
{
    return app(System\System::class);
}

/**
 * todo: invalid naming
 * @return array
 */
function cms_locales()
{
    return array_keys(config('app.locales'));
}

/**
 * todo: invalid naming
 * @param string $name
 * @return string
 */
function cms_option($name)
{
    /** @var Option\RepositoryInterface $repository */
    $repository = app(Option\RepositoryInterface::class);

    return $repository->get($name);
}

/**
 * todo: invalid naming
 * @return array
 */
function cms_options()
{
    /** @var Option\RepositoryInterface $repository */
    $repository = app(Option\RepositoryInterface::class);

    return $repository->getAll();
}

/**
 * Construct form in runtime
 * @param callable $callback
 * @param null $data
 * @return Form\Form
 * @throws Exception
 */
function cms_construct_form(callable $callback, $data = null)
{
    $builder = app(Form\BuilderInterface::class);
    if ($data !== null) {
        $builder->setDataSource($data);
    }
    $fields = call_user_func($callback, $builder, $data);
    $builder->addMany($fields);
    return $builder->build();
}

/**
 * Create form from factory
 * @param string $form
 * @param mixed $dataSource
 * @param array|null $provided
 * @return Form\Form
 */
function cms_create_form($form, $dataSource = null, array $provided = null)
{
    /** @var Form\AbstractFactory $factory */
    $factory = app($form);
    if ($provided !== null) {
        $factory->provideMany($provided);
    }
    return $factory->create($dataSource);
}

//function cms_add_css($href)
//{
//    $asset = app(System\AssetInterface::class);
//    $hrefArr = (array)$href;
//    foreach ($hrefArr as $href) {
//        $asset->addCss($href);
//    }
//}
//
//function cms_add_js($src)
//{
//    $asset = app(System\AssetInterface::class);
//    $srcArr = (array)$src;
//    foreach ($srcArr as $src) {
//        $asset->addJs($src);
//    }
//}
//
//function cms_render_css()
//{
//    return app(System\AssetInterface::class)->renderCss();
//}
//
//function cms_render_js()
//{
//    return app(System\AssetInterface::class)->renderJs();
//}

/**
 * @param string $name
 * @param array|null $data
 * @return bool
 */
function cms_send_template($name, array $data = null)
{
    $logger = app(LoggerInterface::class);

    $template = app(EmailTemplate\TemplateRepository::class)->findByName($name);
    if ($template === null) {
        $logger->error('Email template not found', compact('name'));
        return false;
    }

    $mailer = app(EmailTemplate\MailerInterface::class);

    $mailer->setLogger($logger);

    return $mailer->send($template, $data);

    /*
    $logger = app(LoggerInterface::class);
    $template->setLogger($logger);
    return $template->send($data);
    */
}

/**
 * @param string $page
 * @param SeoInterface $model
 * @param array $replacePairs
 * @return Seo\SeoManagerInterface
 */
function cms_seo($page = null, SeoInterface $model = null, array $replacePairs = [])
{
    $seo = app(Seo\SeoManagerInterface::class);
    if ($page !== null) {
        $seo->fromPage($page);
    }
    if ($model !== null) {
        $seo->fromModel($model);
    }
    if (count($replacePairs)) {
        $seo->replacePairs($replacePairs);
    }

    return $seo;
}

/* ---------------------------------------------------------------------------------------------------------------------
 * Notifications
 */

// TODO:

/* ---------------------------------------------------------------------------------------------------------------------
 * Images
 */

//function cms_image_save($path, $file)
//{
//    $manager = app(ImageManagerInterface::class);
//    if ($file instanceof UploadedFile) {
//        return $manager->storeUploadedFile($file, $path);
//    } elseif ($file instanceof \SplFileInfo) {
//        return $manager->storeFile($file, $path);
//    }
//    throw new \Exception('Invalid file type');
//}
//
//function cms_image_verbose($path, $fileName)
//{
//    $manager = app(ImageManagerInterface::class);
//    return $manager->verbose($fileName, $path);
//}
//
//function cms_image_delete($path, $fileName)
//{
//    $manager = app(ImageManagerInterface::class);
//    $manager->delete($fileName, $path);
//}

/* ---------------------------------------------------------------------------------------------------------------------
 * Uploads
 */

//function cms_upload_save($path, $file)
//{
//    $manager = app(UploadManagerInterface::class);
//    if ($file instanceof UploadedFile) {
//        return $manager->storeUploadedFile($file, $path);
//    } elseif ($file instanceof \SplFileInfo) {
//        return $manager->storeFile($file, $path);
//    }
//    throw new \Exception('Invalid file type');
//}
//
//function cms_upload_verbose($path, $fileName, $local = false)
//{
//    $manager = app(UploadManagerInterface::class);
//    return $manager->verbose($fileName, $path, $local);
//}
//
//function cms_upload_delete($path, $fileName)
//{
//    $manager = app(UploadManagerInterface::class);
//    $manager->delete($fileName, $path);
//}

