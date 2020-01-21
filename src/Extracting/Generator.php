<?php

namespace Thunken\DocDocGoose\Extracting;

use Faker\Factory;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use ReflectionClass;

class Generator extends \Mpociot\ApiDoc\Extracting\Generator
{

    public function processRoute(Route $route, array $rulesToApply = [])
    {
        $route = parent::processRoute($route, $rulesToApply);
        $route['request'] = $this->fetchRequest($route['cleanQueryParameters']);

        return $route;
    }

    /**
     * @param array $queryParameters
     * @return null|string
     */
    protected function fetchRequest(array $queryParameters)
    {
        if (empty($queryParameters)) {
            return null;
        }

        $parameters = '';
        foreach ($queryParameters as $parameter => $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    $parameters .= sprintf("&%s[]=%s", $parameter, $val);
                }
                continue;
            }
            $parameters .= "&$parameter=$value";
        }

        return ltrim($parameters, '&');
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