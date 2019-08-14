<?php namespace Assetic\Test\Extension\Twig;

use PHPUnit\Framework\TestCase;
use Assetic\Extension\Twig\TwigResource;

class TwigResourceTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not installed.');
        }
    }

    public function testInvalidTemplateNameGetContent()
    {
        $loader = $this->prophesize('Twig_LoaderInterface');
        if (!method_exists('Twig_LoaderInterface', 'getSourceContext')) {
            $loader->willImplement('Twig_SourceContextLoaderInterface');
        }

        $loader->getSourceContext('asdf')->willThrow(new \Twig\Error\LoaderError(''));

        $resource = new TwigResource($loader->reveal(), 'asdf');
        $this->assertEquals('', $resource->getContent());
    }

    public function testInvalidTemplateNameIsFresh()
    {
        $loader = $this->getMockBuilder('Twig_LoaderInterface')->getMock();
        $loader->expects($this->once())
            ->method('isFresh')
            ->with('asdf', 1234)
            ->will($this->throwException(new \Twig\Error\LoaderError('')));

        $resource = new TwigResource($loader, 'asdf');
        $this->assertFalse($resource->isFresh(1234));
    }
}
