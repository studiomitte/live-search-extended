<?php
declare(strict_types=1);

namespace GeorgRinger\Doc;

use StudioMitte\LiveSearchExtended\Provider\FormDataProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Form\Domain\Model\FormDefinition;

return static function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder) {
    $services = $configurator->services();
    if (class_exists(FormDefinition::class)) {
        $services->set(FormDataProvider::class)
            ->public()
            ->autowire()
            ->autoconfigure()
            ->tag('livesearch.provider', [
                'priority' => 5
            ])
        ;
    } else {
        $services->set(FormDataProvider::class)
            ->autowire(false)
            ->autoconfigure(false)
        ;
    }

};