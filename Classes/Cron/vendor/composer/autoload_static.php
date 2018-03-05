<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit350e637e372c530a484862a1d7269ecf
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'GO\\' => 3,
        ),
        'C' => 
        array (
            'Cron\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'GO\\' => 
        array (
            0 => __DIR__ . '/..' . '/peppeocchi/php-cron-scheduler/src/GO',
        ),
        'Cron\\' => 
        array (
            0 => __DIR__ . '/..' . '/mtdowling/cron-expression/src/Cron',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit350e637e372c530a484862a1d7269ecf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit350e637e372c530a484862a1d7269ecf::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}