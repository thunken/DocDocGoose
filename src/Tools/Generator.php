<?php

namespace Thunken\DocDocGoose\Tools;

use Faker\Factory;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
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
     * @param array $tags
     *
     * @return array
     */
    protected function getQueryParametersFromDocBlock(array $tags)
    {
        $parameters = collect($tags)
            ->filter(function ($tag) {
                return $tag instanceof Tag && $tag->getName() === 'queryParam';
            })
            ->mapWithKeys(function ($tag) {
                preg_match('/(.+?)\s+(.+?)\s+(required\s+)?(.*)/', $tag->getContent(), $content);
                if (empty($content)) {
                    // this means only name was supplied
                    list($name, $type) = preg_split('/\s+/', $tag->getContent());
                    $required = false;
                    $description = '';
                } else {
                    list($_, $name, $type, $required, $description) = $content;
                    $description = trim($description);
                    if ($description == 'required' && empty(trim($required))) {
                        $required = $description;
                        $description = '';
                    }
                    $required = trim($required) == 'required' ? true : false;
                }

                $type = $this->normalizeParameterType($type);
                list($description, $value) = $this->parseDescription($description, 'string');
                if (is_null($value)) {
                    $value = Str::contains($description, ['number', 'count', 'page'])
                        ? $this->generateDummyValue('integer')
                        : $this->generateDummyValue('string');
                }

                return [$name => compact('type', 'description', 'required', 'value')];
            })->toArray();

        return $parameters;
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

    protected function normalizeParameterType($type)
    {
        $typeMap = [
            'int' => 'integer',
            'bool' => 'boolean',
            'double' => 'float',
        ];

        return $type ? ($typeMap[$type] ?? $type) : 'string';
    }

    /**
     * Allows users to specify an example for the parameter by writing 'Example: the-example',
     * to be used in example requests and response calls.
     *
     * @param string $description
     * @param string $type The type of the parameter. Used to cast the example provided, if any.
     *
     * @return array The description and included example.
     */
    protected function parseDescription(string $description, string $type)
    {
        $example = null;
        if (preg_match('/(.*)\s+Example:\s*(.*)\s*/', $description, $content)) {
            $description = $content[1];

            // examples are parsed as strings by default, we need to cast them properly
            $example = $this->castToType($content[2], $type);
        }

        return [$description, $example];
    }

    /**
     * Cast a value from a string to a specified type.
     *
     * @param string $value
     * @param string $type
     *
     * @return mixed
     */
    protected function castToType(string $value, string $type)
    {
        $casts = [
            'integer' => 'intval',
            'number' => 'floatval',
            'float' => 'floatval',
            'boolean' => 'boolval',
        ];

        // First, we handle booleans. We can't use a regular cast,
        //because PHP considers string 'false' as true.
        if ($value == 'false' && $type == 'boolean') {
            return false;
        }

        if (isset($casts[$type])) {
            return $casts[$type]($value);
        }

        return $value;
    }

    private function generateDummyValue(string $type)
    {
        $faker = Factory::create();
        $fakes = [
            'integer' => function () {
                return rand(1, 20);
            },
            'number' => function () use ($faker) {
                return $faker->randomFloat();
            },
            'float' => function () use ($faker) {
                return $faker->randomFloat();
            },
            'boolean' => function () use ($faker) {
                return $faker->boolean();
            },
            'string' => function () use ($faker) {
                return Str::random();
            },
            'array' => function () {
                return '[]';
            },
            'object' => function () {
                return '{}';
            },
        ];

        $fake = $fakes[$type] ?? $fakes['string'];

        return $fake();
    }

}