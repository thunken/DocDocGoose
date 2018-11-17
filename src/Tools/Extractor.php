<?php

namespace Thunken\DocDocGoose\Tools;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Thunken\DocDocGoose\Extracts\DocLine;
use Thunken\DocDocGoose\Extracts\Group;

class Extractor
{

    /** @var array $config */
    private $config = [];

    /** @var Collection $groups */
    private $groups;

    /**
     * Extractor constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->groups = new Collection();
    }

    /**
     * Extract and store all route groups that match configure route patterns
     *
     * @return Extractor
     */
    public function extract()
    {
        /** @var Route $route */
        foreach(\Route::getRoutes() as $route) {
            // @TODO Manage api version by adding one more array level
            if ($route->named($this->toBeExtracted())) {
                $generator = new Generator();
                $rules = $this->getRules();
                $doc = $generator->processRoute($route, $rules);
                $groupId = md5($doc['group']);
                $groupPath = sprintf('%s', Str::slug($doc['group']));

                $group = $this->groups->filter(function($item) use ($groupId) {
                    return ($item->getId() == $groupId);
                })->first();

                if (!($group instanceof Group)) {
                    $group = new Group();
                    $group->setName($doc['group']);
                    $group->setId($groupId);
                    $group->setPath($groupPath);
                    $this->groups->push($group);
                }

                $docPath = Str::slug(str_replace('/', '-', $doc['uri']));
                $doc['path'] = sprintf('%s-%s', $groupPath, $docPath);

                $group->push(new DocLine($doc));
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function toRaw()
    {
        return $this->groups;
    }

    /**
     * Returns documentation as groups array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->groups->toArray();
    }

    /**
     * Render documentation menu
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderMenu()
    {
        $this->extract();
        return view(
            'docdocgoose::html.menu',
            [ 'groups' => $this->groups ]
        );
    }

    /**
     * Render documentation content
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderContent()
    {
        $this->extract();
        return view(
            'docdocgoose::html.content',
            [ 'groups' => $this->groups ]
        );
    }

    /**
     * Get the configures api route patterns to be extracted
     *
     * @return array
     */
    private function toBeExtracted()
    {
        return $this->config['routes']['patterns'];
    }

    /**
     *
     *
     * @return array
     */
    private function getRules()
    {
        return $this->config['rules'];
    }

}