<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9c8748252360de9c3f5d46e3fca792de
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Apps4every\\ExtendDatabase\\' => 26,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Apps4every\\ExtendDatabase\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9c8748252360de9c3f5d46e3fca792de::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9c8748252360de9c3f5d46e3fca792de::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9c8748252360de9c3f5d46e3fca792de::$classMap;

        }, null, ClassLoader::class);
    }
}
