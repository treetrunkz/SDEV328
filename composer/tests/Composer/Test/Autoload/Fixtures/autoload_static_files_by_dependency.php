<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitFilesAutoloadOrder
{
    public static $files = array (
        'bfdd693009729d60c830ff8d79129635' => __DIR__ . '/..' . '/c/lorem/testC.php',
        '61e6098c8cafe404d6cf19e59fc2b788' => __DIR__ . '/..' . '/d/d/testD.php',
        '8bceec6fdc149a1a6acbf7ad3cfed51c' => __DIR__ . '/..' . '/z/foo/testA.php',
        'c5466e580c6c2403f225c43b6a21a96f' => __DIR__ . '/..' . '/b/bar/testB.php',
        '69dfc37c40a853a7cbac6c9d2367c5f4' => __DIR__ . '/..' . '/e/e/testE.php',
        'ab280164f4754f5dfdb0721de84d737f' => __DIR__ . '/../..' . '/root2.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitFilesAutoloadOrder::$classMap;

        }, null, ClassLoader::class);
    }
}
