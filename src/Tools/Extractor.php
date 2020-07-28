<?php

namespace Thunken\DocDocGoose\Tools;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mpociot\ApiDoc\Tools\DocumentationConfig;
use Thunken\DocDocGoose\Extracting\Generator;
use Thunken\DocDocGoose\Extracts\DocLine;
use Thunken\DocDocGoose\Extracts\Group;
use Thunken\DocDocGoose\Extracts\Version;

class Extractor
{

    /** @var array $config */
    private $config = [];

    /** @var Collection $versions */
    private $versions;

    CONST cacheName = 'docdocgoose_versions';

    /**
     * Extractor constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->versions = $this->getCachedVersions();
    }

    /**
     * Extract and store all route groups that match configure route patterns
     *
     * @return Extractor
     * @throws \Exception
     */
    public function extract()
    {
        // Already extracted, aborting
        if ($this->versions->count() > 0) {
            return $this;
        }

        $versionsToBeExtracted = $this->toBeExtracted();
        foreach ($versionsToBeExtracted as $versionName => $routeToBeExtracted) {
            $version = $this->getVersion($versionName);
            $this->extractGroups($version);
        }

        $this->setVersionsOnCache();

        return $this;
    }

    /**
     * @return Collection
     */
    public function toRaw()
    {
        return $this->versions;
    }

    /**
     * Returns documentation as groups array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->versions->toArray();
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
            [ 'versions' => $this->versions ]
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
            [ 'versions' => $this->versions ]
        );
    }

    /**
     * Get the documentation cache repository
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function getCacheRepository()
    {
        return $this->getCacheRepository();
    }

    /**
     * @param Version $version
     * @return $this
     */
    private function extractGroups(Version $version)
    {
        foreach(\Route::getRoutes() as $route) {
            if (!$route->named($version->getPatterns())) {
                continue;
            }

            $generator = new Generator(new DocumentationConfig(config('docdocgoose')));
            $rules = $version->getRules();
            $doc = $generator->processRoute($route, $rules);
            $group = $this->getGroup($version, $doc);

            $docPath = Str::slug(str_replace('/', '-', $doc['uri']));
            $doc['path'] = sprintf('%s-%s', $group->getPath(), $docPath);
            $group->push(new DocLine($doc));
        }

        return $this;
    }

    /**
     * @param $versionName
     * @return Version
     * @throws \Exception
     */
    private function getVersion($versionName)
    {
        $version = $this->versions->filter(function($item) use ($versionName) {
            return ($item->getId() == $versionName);
        })->first();

        if (!($version instanceof Version)) {
            $version = new Version();
            $version->setName($versionName);
            $version->setId($versionName);
            $version->setPath($versionName);
            $version->setPatterns($this->getVersionPatterns($versionName));
            $version->setRules($this->getVersionRules($versionName));
            $this->versions->push($version);
        }

        return $version;
    }

    private function getGroup(Version $version, $doc)
    {
        $groupId = md5($doc['metadata']['groupName']);

        $group = $version->filter(function($item) use ($groupId) {
            return ($item->getId() == $groupId);
        })->first();

        if (!($group instanceof Group)) {
            $groupPath = sprintf(
                '%s-%s',
                $version->getName(),
                Str::slug($doc['metadata']['groupName'])
            );

            $group = new Group();
            $group->setName($doc['metadata']['groupName']);
            $group->setId($groupId);
            $group->setPath($groupPath);
            $group->setVersion($version->getName());
            $version->push($group);
        }

        return $group;
    }

    /**
     * Get the configures api route patterns to be extracted
     *
     * @return array
     */
    private function toBeExtracted()
    {
        return $this->config['routes'];
    }

    /**
     * @param $version
     * @return array
     * @throws \Exception
     */
    private function getVersionRules($version)
    {
        $this->checkVersion($version);

        return $this->config['routes'][$version]['rules'];
    }

    /**
     * @param $version
     * @return mixed
     * @throws \Exception
     */
    private function getVersionPatterns($version)
    {
        $this->checkVersion($version);

        return $this->config['routes'][$version]['patterns'];
    }

    /**
     * @param $version
     * @throws \Exception
     */
    private function checkVersion($version)
    {
        if (!isset($this->config['routes'][$version])) {
            throw new \Exception('Version not configured.');
        }
    }

    private function getCachedVersions()
    {
        if (!$this->shouldCache()) {
            return new Collection();
        }

        $versions = $this->getCacheRepository()->get(self::cacheName);
        if (null === $versions) {
            return new Collection();
        }

        return $versions;
    }

    private function shouldCache()
    {
        if (!isset($this->config['cache'])) {
            return false;
        }

        if (!isset($this->config['cache']['enabled'])) {
            return false;
        }

        if (true !== $this->config['cache']['enabled']) {
            return false;
        }

        if ('' == $this->getCacheStore()) {
            return false;
        }

        return true;
    }

    private function getCacheStore()
    {
        if (
            !isset($this->config['cache']['store']) ||
            '' == $this->config['cache']['store']
        ) {
            return null;
        }

        return $this->config['cache']['store'];
    }

    private function setVersionsOnCache()
    {
        if (!$this->shouldCache()) {
            return $this;
        }

        $this->getCacheRepository()
            ->forever(self::cacheName, $this->versions);

        return $this;
    }

}