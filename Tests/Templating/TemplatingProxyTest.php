<?php

/*
 * This file is part of the Mremi\TemplatingExtraBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\TemplatingExtraBundle\Tests\Templating;

use Mremi\TemplatingExtraBundle\Templating\TemplatingProxy;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests TemplatingProxy class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class TemplatingProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $templating;

    /**
     * @var \Symfony\Component\Templating\TemplateNameParserInterface
     */
    private $templateNameParser;

    /**
     * @var \Symfony\Component\Config\FileLocatorInterface
     */
    private $templateLocator;

    /**
     * @var \Symfony\Component\Stopwatch\Stopwatch
     */
    private $stopwatch;

    /**
     * @var \Mremi\TemplatingExtraBundle\DataCollector\TemplatingDataCollector
     */
    private $dataCollector;

    /**
     * @var TemplatingProxy
     */
    private $proxy;

    /**
     * @var \Symfony\Component\Stopwatch\StopwatchEvent
     */
    private $event;

    /**
     * Tests the renderResponse method
     */
    public function testRenderResponse()
    {
        $this->proxy = $this->getMockBuilder('Mremi\TemplatingExtraBundle\Templating\TemplatingProxy')
            ->setConstructorArgs(array($this->templating, $this->templateNameParser, $this->templateLocator, $this->stopwatch, $this->dataCollector))
            ->setMethods(array('startProfiling', 'stopProfiling'))
            ->getMock();

        $this->proxy
            ->expects($this->once())
            ->method('startProfiling')
            ->with($this->equalTo('DummyBundle:Controller:view.html.twig'), $this->equalTo(array('param1' => 'value1')))
            ->will($this->returnValue($this->event));

        $response = new Response('Test');

        $this->templating
            ->expects($this->once())
            ->method('renderResponse')
            ->with($this->equalTo('DummyBundle:Controller:view.html.twig'), $this->equalTo(array('param1' => 'value1')), $this->equalTo($response))
            ->will($this->returnValue($response));

        $this->proxy
            ->expects($this->once())
            ->method('stopProfiling')
            ->with($this->equalTo($this->event));

        $rendered = $this->proxy->renderResponse('DummyBundle:Controller:view.html.twig', array('param1' => 'value1'), $response);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $rendered);
        $this->assertEquals($response->getContent(), $rendered->getContent());
    }

    /**
     * Tests the render method
     */
    public function testRender()
    {
        $this->proxy = $this->getMockBuilder('Mremi\TemplatingExtraBundle\Templating\TemplatingProxy')
            ->setConstructorArgs(array($this->templating, $this->templateNameParser, $this->templateLocator, $this->stopwatch, $this->dataCollector))
            ->setMethods(array('startProfiling', 'stopProfiling'))
            ->getMock();

        $this->proxy
            ->expects($this->once())
            ->method('startProfiling')
            ->with($this->equalTo('DummyBundle:Controller:view.html.twig'), $this->equalTo(array('param1' => 'value1')))
            ->will($this->returnValue($this->event));

        $response = new Response('Test');

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->with($this->equalTo('DummyBundle:Controller:view.html.twig'), $this->equalTo(array('param1' => 'value1')))
            ->will($this->returnValue($response));

        $this->proxy
            ->expects($this->once())
            ->method('stopProfiling')
            ->with($this->equalTo($this->event));

        $rendered = $this->proxy->render('DummyBundle:Controller:view.html.twig', array('param1' => 'value1'));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $rendered);
        $this->assertEquals($response->getContent(), $rendered->getContent());
    }

    /**
     * Tests the exists method
     */
    public function testExists()
    {
        $this->templating
            ->expects($this->once())
            ->method('exists')
            ->with($this->equalTo('DummyBundle:Controller:view.html.twig'));

        $this->proxy->exists('DummyBundle:Controller:view.html.twig');
    }

    /**
     * Tests the supports method
     */
    public function testSupports()
    {
        $this->templating
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo('DummyBundle:Controller:view.html.twig'));

        $this->proxy->supports('DummyBundle:Controller:view.html.twig');
    }

    /**
     * Provides data for testStartProfiling
     *
     * @return array
     */
    public function getTemplates()
    {
        return array(
            array('DummyBundle:Controller:view.html.twig'),
            array(new TemplateReference('DummyBundle', 'Controller', 'view', 'html', 'twig')),
        );
    }

    /**
     * Tests the startProfiling method
     *
     * @param mixed $template
     *
     * @dataProvider getTemplates
     */
    public function testStartProfiling($template)
    {
        if ($template instanceof TemplateReference) {
            $templateReference = $template;
        } else {
            $templateReference = $this->getMock('Symfony\Component\Templating\TemplateReferenceInterface');
            $templateReference
                ->expects($this->exactly(2))
                ->method('getLogicalName')
                ->will($this->returnValue('DummyBundle:Controller:view.html.twig'));

            $this->templateNameParser
                ->expects($this->once())
                ->method('parse')
                ->with($this->equalTo('DummyBundle:Controller:view.html.twig'))
                ->will($this->returnValue($templateReference));
        }

        $property = new \ReflectionProperty($this->proxy, 'trace');
        $property->setAccessible(true);

        $this->assertEquals(array(), $property->getValue($this->proxy));

        $this->templateLocator
            ->expects($this->once())
            ->method('locate')
            ->with($this->equalTo($templateReference))
            ->will($this->returnValue('/var/www/project/src/Dummy/Resources/views/view.html.twig'));

        $this->stopwatch
            ->expects($this->once())
            ->method('start');

        $method = new \ReflectionMethod($this->proxy, 'startProfiling');
        $method->setAccessible(true);

        $method->invoke($this->proxy, $template, array('param1' => 'value1'));

        $trace = $property->getValue($this->proxy);

        $this->assertArrayHasKey('name', $trace);
        $this->assertEquals('DummyBundle:Controller:view.html.twig', $trace['name']);

        $this->assertArrayHasKey('file', $trace);
        $this->assertEquals('/var/www/project/src/Dummy/Resources/views/view.html.twig', $trace['file']);

        $this->assertArrayHasKey('parameters', $trace);
        $this->assertEquals(array('param1' => 'value1'), $trace['parameters']);

        $this->assertArrayHasKey('duration', $trace);
        $this->assertNull($trace['duration']);

        $this->assertArrayHasKey('memory_start', $trace);
        $this->assertTrue(is_int($trace['memory_start']));

        $this->assertArrayHasKey('memory_end', $trace);
        $this->assertNull($trace['memory_end']);

        $this->assertArrayHasKey('memory_peak', $trace);
        $this->assertNull($trace['memory_peak']);
    }

    /**
     * Tests the stopProfiling method
     */
    public function testStopProfiling()
    {
        $this->event
            ->expects($this->once())
            ->method('getDuration')
            ->will($this->returnValue(10));

        $this->event
            ->expects($this->once())
            ->method('stop');

        $this->dataCollector
            ->expects($this->once())
            ->method('addTrace')
            ->with($this->callback(function($trace) {
                return is_array($trace) &&
                    array_key_exists('duration', $trace) && 10 === $trace['duration'] &&
                    array_key_exists('memory_end', $trace) && is_int($trace['memory_end']) &&
                    array_key_exists('memory_peak', $trace) && is_int($trace['memory_peak']);
            }));

        $method = new \ReflectionMethod($this->proxy, 'stopProfiling');
        $method->setAccessible(true);

        $method->invoke($this->proxy, $this->event);
    }

    /**
     * Initializes properties used by tests
     */
    protected function setUp()
    {
        $this->templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');

        $this->templateNameParser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');

        $this->templateLocator = $this->getMock('Symfony\Component\Config\FileLocatorInterface');

        $this->stopwatch = $this->getMockBuilder('Symfony\Component\Stopwatch\Stopwatch')
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataCollector = $this->getMockBuilder('Mremi\TemplatingExtraBundle\DataCollector\TemplatingDataCollector')
            ->disableOriginalConstructor()
            ->getMock();

        $this->proxy = new TemplatingProxy($this->templating, $this->templateNameParser, $this->templateLocator, $this->stopwatch, $this->dataCollector);

        $this->event = $this->getMockBuilder('Symfony\Component\Stopwatch\StopwatchEvent')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Cleanups properties
     */
    protected function tearDown()
    {
        $this->templating         = null;
        $this->templateNameParser = null;
        $this->templateLocator    = null;
        $this->stopwatch          = null;
        $this->dataCollector      = null;
        $this->proxy              = null;
        $this->event              = null;
    }
}
