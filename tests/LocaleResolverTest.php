<?php

namespace CommerceGuys\Intl\Tests;

use org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \CommerceGuys\Intl\LocaleResolverTrait
 */
class LocaleResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DummyRepository
     */
    protected $repository;

    public function setUp()
    {
        // Simulate the presence of various definitions.
        $root = vfsStream::setup('resources');
        vfsStream::newFile('dummy/bs-Cyrl.json')->at($root)->setContent('');
        vfsStream::newFile('dummy/bs.json')->at($root)->setContent('');
        vfsStream::newFile('dummy/en.json')->at($root)->setContent('');

        $this->repository = new DummyRepository();
    }

    /**
     * @covers ::getDefaultLocale
     * @covers ::setDefaultLocale
     *
     * @uses \CommerceGuys\Intl\LocaleResolverTrait::getDefaultLocale
     * @uses \CommerceGuys\Intl\LocaleResolverTrait::setDefaultLocale
     */
    public function testDefaultLocale()
    {
        $this->assertEquals('en', $this->repository->getDefaultLocale());
        $this->repository->setDefaultLocale('fr');
        $this->assertEquals('fr', $this->repository->getDefaultLocale());
    }

    /**
     * @covers ::getFallbackLocale
     * @covers ::setFallbackLocale
     *
     * @uses \CommerceGuys\Intl\LocaleResolverTrait::getFallbackLocale
     * @uses \CommerceGuys\Intl\LocaleResolverTrait::setFallbackLocale
     */
    public function testFallbackLocale()
    {
        $this->assertNull($this->repository->getFallbackLocale());
        $this->repository->setFallbackLocale('en');
        $this->assertEquals('en', $this->repository->getFallbackLocale());
    }

    /**
     * @covers ::resolveLocale
     */
    public function testLocaleFallback()
    {
        $locale = $this->repository->runResolveLocale('bs-Cyrl-BA');
        $this->assertEquals('bs-Cyrl', $locale);
        $locale = $this->repository->runResolveLocale('bs-Latn-BA');
        $this->assertEquals('bs', $locale);
        $locale = $this->repository->runResolveLocale('de', 'en');
        $this->assertEquals('en', $locale);
        $locale = $this->repository->runResolveLocale();
        $this->assertEquals('en', $locale);
    }

    /**
     * @covers ::resolveLocale
     * @expectedException \CommerceGuys\Intl\Exception\UnknownLocaleException
     */
    public function testInvalidLocale()
    {
        $locale = $this->repository->runResolveLocale('de');
    }
}
