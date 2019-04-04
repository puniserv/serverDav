<?php
declare(strict_types=1);

namespace Src;

use Src\Action\Actions;
use Src\Action\Factory;

class Request
{
    /** @var Factory */
    private $factory;
    /** @var array */
    private $uriParts;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return Action\Action
     * @throws ServerException
     */
    public function getAction(): Action\Action
    {
        return $this->factory->get($this->getFirstPartOfUrl());
    }

    private function getFirstPartOfUrl(): string
    {
        return $this->getUriParts()[0] ?? '';
    }

    private function getUriParts(): array
    {
        if($this->uriParts){
            return $this->uriParts;
        }
        $uri = $this->getServer()['REQUEST_URI'] ?? '';
        $queryPosition = strpos($uri, '?');
        if($queryPosition !== false){
            $uri = substr($uri, 0, $queryPosition);
        }
        return $this->uriParts = array_values(array_filter(explode('/', $uri)));
    }

    private function getServer(): array
    {
        return $_SERVER;
    }

    public function validateQuery(): bool
    {
        return Actions::checkIfActionExist($this->getFirstPartOfUrl());
    }
}
