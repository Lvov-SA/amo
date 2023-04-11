<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class addLeadFactory
{
    public function __invoke(ContainerInterface $container) : addLead
    {
        return new addLead($container->get(TemplateRendererInterface::class));
    }
}
