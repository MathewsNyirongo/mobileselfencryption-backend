<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9c99d745f222d74235d97e18a988e68a
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9c99d745f222d74235d97e18a988e68a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9c99d745f222d74235d97e18a988e68a::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
