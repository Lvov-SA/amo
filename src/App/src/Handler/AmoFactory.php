<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class AmoFactory
{
    public function __invoke(ContainerInterface $container) : Amo
    {
        return new Amo($container->get(TemplateRendererInterface::class));
    }
}
