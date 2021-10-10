<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters

    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__ . '/alunni',
        __DIR__ . '/assemblee',
        __DIR__ . '/bots',
        __DIR__ . '/cattedre',
        __DIR__ . '/certcomp',
        __DIR__ . '/circolari',
        __DIR__ . '/classi',
        __DIR__ . '/collegamenti',
        __DIR__ . '/colloqui',
        __DIR__ . '/comuni',
        __DIR__ . '/consorientativo',
        __DIR__ . '/contr',
        __DIR__ . '/docenti',
        __DIR__ . '/documenti',
        __DIR__ . '/esame3m',
        __DIR__ . '/evacuazione',
        __DIR__ . '/ferie',
        __DIR__ . '/importa',
        __DIR__ . '/install',
        __DIR__ . '/lezioni',
        __DIR__ . '/lezionigruppo',
        __DIR__ . '/lib',
        __DIR__ . '/login',
        __DIR__ . '/lsapp',
        __DIR__ . '/materie',
        __DIR__ . '/moodle',
        __DIR__ . '/note',
        __DIR__ . '/obiettivi',
        __DIR__ . '/password',
        __DIR__ . '/pei',
        __DIR__ . '/programmazione',
        __DIR__ . '/progrcert',
        __DIR__ . '/regclasse',
        __DIR__ . '/rp',
        __DIR__ . '/scrutini',
        __DIR__ . '/segreteria',
        __DIR__ . '/sms',
        __DIR__ . '/valutazionecomportamento',
        __DIR__ . '/valutazioni',
    ]);


    // Rector is using static reflection to load code without running it - see https://phpstan.org/blog/zero-config-analysis-with-static-reflection
    $parameters->set(Option::AUTOLOAD_PATHS, [
        // discover specific file
        //__DIR__ . '/file-with-functions.php',
        // or full directory
        __DIR__ . '/alunni',
        __DIR__ . '/assemblee',
        __DIR__ . '/bots',
        __DIR__ . '/cattedre',
        __DIR__ . '/certcomp',
        __DIR__ . '/circolari',
        __DIR__ . '/classi',
        __DIR__ . '/collegamenti',
        __DIR__ . '/colloqui',
        __DIR__ . '/comuni',
        __DIR__ . '/consorientativo',
        __DIR__ . '/contr',
        __DIR__ . '/docenti',
        __DIR__ . '/documenti',
        __DIR__ . '/esame3m',
        __DIR__ . '/evacuazione',
        __DIR__ . '/ferie',
        __DIR__ . '/importa',
        __DIR__ . '/install',
        __DIR__ . '/lezioni',
        __DIR__ . '/lezionigruppo',
        __DIR__ . '/lib',
        __DIR__ . '/login',
        __DIR__ . '/lsapp',
        __DIR__ . '/materie',
        __DIR__ . '/moodle',
        __DIR__ . '/note',
        __DIR__ . '/obiettivi',
        __DIR__ . '/password',
        __DIR__ . '/pei',
        __DIR__ . '/programmazione',
        __DIR__ . '/progrcert',
        __DIR__ . '/regclasse',
        __DIR__ . '/rp',
        __DIR__ . '/scrutini',
        __DIR__ . '/segreteria',
        __DIR__ . '/sms',
        __DIR__ . '/valutazionecomportamento',
        __DIR__ . '/valutazioni',
    ]);


    $parameters->set(Option::BOOTSTRAP_FILES, [
        //__DIR__ . '/php-ini.php',
    ]);


    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_74);

    // Define what rule sets will be applied
    $containerConfigurator->import(SetList::PHP_56);

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
