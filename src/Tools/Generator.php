<?php

namespace Thunken\DocDocGoose\Tools;

use Illuminate\Routing\Route;
use Mpociot\Reflection\DocBlock\Tag;
use ReflectionClass;

class Generator extends \Mpociot\ApiDoc\Tools\Generator
{

    public function processRoute(Route $route, array $rulesToApply = [])
    {
        $parsedRoute = parent::processRoute($route, $rulesToApply);

        $routeAction = $route->getAction();
        list($class, $method) = explode('@', $routeAction['uses']);
        $controller = new ReflectionClass($class);
        $method = $controller->getMethod($method);

        $routeGroup = $this->getRouteGroup($controller, $method);
        $docBlock = $this->parseDocBlock($method);

        $parsedRoute['request'] = $this->getRequestFromDocBlock($docBlock['tags']);

        return $parsedRoute;
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    protected function getRequestFromDocBlock(array $tags)
    {
        $request = collect($tags)
            ->first(function ($tag) {
                return $tag instanceof Tag && strtolower($tag->getName()) === 'request';
            });

        if (!($request instanceof Tag)) {
            return '';
        }

        return (string) $request->getContent();
    }

}