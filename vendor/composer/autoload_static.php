<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfe41bb7e9227dbea95c890296eaffa1a
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Component\\Finder\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Component\\Finder\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/finder',
        ),
    );

    public static $prefixesPsr0 = array (
        'O' => 
        array (
            'OOSSH' => 
            array (
                0 => __DIR__ . '/..' . '/youknowriad/oossh/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfe41bb7e9227dbea95c890296eaffa1a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfe41bb7e9227dbea95c890296eaffa1a::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitfe41bb7e9227dbea95c890296eaffa1a::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
