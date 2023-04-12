<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class Amo implements RequestHandlerInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    public function __construct(TemplateRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return new HtmlResponse(
            "<form action='/addLead' method='GET'>
            <input type = 'text' name = 'name' placeholder = 'имя'> <br> 
            <input type = 'text' name = 'mail' placeholder = 'email'> <br> 
            <input type = 'text' name = 'number' placeholder = 'телефон'> <br>
            <input type = 'text' name = 'price' placeholder = 'цена'> <br>
            <input type = 'submit' value = 'add'><br>
            </form>"
        );
    }
}
