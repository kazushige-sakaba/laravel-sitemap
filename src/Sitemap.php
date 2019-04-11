<?php

namespace Spatie\Sitemap;

use Spatie\Sitemap\Tags\Tag;
use Spatie\Sitemap\Tags\Url;

class Sitemap
{
    /** @var array */
    protected $tags = [];

    /** @var bool|int */
    protected $setDisplayToLastmod = true;

    /** @var bool|int */
    protected $setDisplayToChangefreq = true;

    /** @var bool|int */
    protected $setDisplayToPriority = true;

    public static function create(): self
    {
        return new static();
    }

    /**
     * @param string|\Spatie\Sitemap\Tags\Tag $tag
     *
     * @return $this
     */
    public function add($tag): self
    {
        if (is_string($tag)) {
            $tag = Url::create($tag);
        }

        if (! in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getUrl(string $url): ?Url
    {
        return collect($this->tags)->first(function (Tag $tag) use ($url) {
            return $tag->getType() === 'url' && $tag->url === $url;
        });
    }

    public function hasUrl(string $url): bool
    {
        return (bool) $this->getUrl($url);
    }

    public function render(): string
    {
        sort($this->tags);

        $tags = $this->tags;
        $setDisplayToLastmod = $this->setDisplayToLastmod;
        $setDisplayToChangefreq = $this->setDisplayToChangefreq;
        $setDisplayToPriority = $this->setDisplayToPriority;

        return view('laravel-sitemap::sitemap')
            ->with(compact('tags', 'setDisplayToLastmod', 'setDisplayToChangefreq', 'setDisplayToPriority'))
            ->render();
    }

    public function writeToFile(string $path): self
    {
        file_put_contents($path, $this->render());

        return $this;
    }

    /**
     * Set display/non-display of "Lastmod" of view file.
     *
     * @param bool $setDisplayToLastmod
     *
     * @return $this
     */
    public function setDisplayToLastmod(bool $setDisplayToLastmod)
    {
        $this->setDisplayToLastmod = $setDisplayToLastmod;

        return $this;
    }

    /**
     * Set display/non-display of "Changefreq" of view file.
     *
     * @param bool $setDisplayToChangefreq
     *
     * @return $this
     */
    public function setDisplayToChangefreq(bool $setDisplayToChangefreq)
    {
        $this->setDisplayToChangefreq = $setDisplayToChangefreq;

        return $this;
    }

    /**
     * Set display/non-display of Priority of view file.
     *
     * @param bool $setDisplayToPriority
     *
     * @return $this
     */
    public function setDisplayToPriority(bool $setDisplayToPriority)
    {
        $this->setDisplayToPriority = $setDisplayToPriority;

        return $this;
    }
}
