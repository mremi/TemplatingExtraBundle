<?php

/*
 * This file is part of the Mremi\TemplatingExtraBundle Symfony bundle.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\TemplatingExtraBundle\Tests\DataCollector;

use Mremi\TemplatingExtraBundle\DataCollector\TemplatingDataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Tests TemplatingDataCollector class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class TemplatingDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplatingDataCollector
     */
    private $collector;

    /**
     * Tests the addTrace method
     */
    public function testAddTrace()
    {
        $property = new \ReflectionProperty($this->collector, 'traces');
        $property->setAccessible(true);

        $this->assertEquals(array(), $property->getValue($this->collector));

        $this->collector->addTrace(array(
            'duration'    => 10,
            'memory_peak' => 15360,
        ));

        $this->assertEquals(array(
            array('duration' => 10, 'memory_peak' => 15360),
        ), $property->getValue($this->collector));

        $this->collector->addTrace(array(
            'duration'    => 20,
            'memory_peak' => 20480,
        ));

        $this->assertEquals(array(
            array('duration' => 10, 'memory_peak' => 15360),
            array('duration' => 20, 'memory_peak' => 20480),
        ), $property->getValue($this->collector));
    }

    /**
     * Tests the collect method
     */
    public function testCollect()
    {
        $request   = new Request;
        $response  = new Response;
        $exception = new \Exception;

        $property = new \ReflectionProperty($this->collector, 'data');
        $property->setAccessible(true);

        if (version_compare(Kernel::VERSION, '2.6', '<')) {
            $this->assertNull($property->getValue($this->collector));
        } else {
            $data = $property->getValue($this->collector);

            $this->assertTrue(is_array($data));
            $this->assertCount(0, $data);
        }

        $this->collector->collect($request, $response, $exception);

        $this->assertEquals(array(
            'templates'      => array(),
            'total_duration' => 0,
        ), $property->getValue($this->collector));

        $this->assertEquals(array(), $this->collector->getTemplates());
        $this->assertEquals(0, $this->collector->getTotalDuration());

        $trace1 = array(
            'name'         => 'DummyBundle:Controller:view1.html.twig',
            'file'         => '/var/www/project/src/Dummy/Resources/views/view1.html.twig',
            'parameters'   => array(),
            'duration'     => 10,
            'memory_start' => 10240,
            'memory_end'   => 12288,
            'memory_peak'  => 15360,
        );
        $trace2 = array(
            'name'         => 'DummyBundle:Controller:view2.html.twig',
            'file'         => '/var/www/project/src/Dummy/Resources/views/view2.html.twig',
            'parameters'   => array(
                'param1' => 'value1',
                'param2' => 'value2',
            ),
            'duration'     => 20,
            'memory_start' => 15360,
            'memory_end'   => 17408,
            'memory_peak'  => 20480,
        );

        $this->collector->addTrace($trace1);
        $this->collector->addTrace($trace2);

        $this->collector->collect($request, $response, $exception);

        $this->assertEquals(array(
            'templates'      => array($trace1, $trace2),
            'total_duration' => 30,
        ), $property->getValue($this->collector));

        $this->assertEquals(array($trace1, $trace2), $this->collector->getTemplates());
        $this->assertEquals(30, $this->collector->getTotalDuration());
    }

    /**
     * Tests the getName method
     */
    public function testGetName()
    {
        $this->assertEquals('templating', $this->collector->getName());
    }

    /**
     * Initializes properties used by tests
     */
    protected function setUp()
    {
        $this->collector = new TemplatingDataCollector;
    }

    /**
     * Cleanups properties
     */
    protected function tearDown()
    {
        $this->collector = null;
    }
}
