<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class AddLeadFactory
{
    public function __invoke(ContainerInterface $container) : AddLead
    {
        return new AddLead($container->get(TemplateRendererInterface::class));
    }
}
