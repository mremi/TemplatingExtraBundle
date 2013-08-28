<?php

namespace Mremi\TemplatingExtraBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Templating data collector class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class TemplatingDataCollector extends DataCollector
{
    /**
     * @var array
     */
    private $traces = array();

    /**
     * Adds profiling data
     *
     * @param array $trace An array of profiling information
     */
    public function addTrace(array $trace)
    {
        $this->traces[] = $trace;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'templates'      => array(),
            'total_duration' => 0,
        );

        foreach ($this->traces as $trace) {
            $this->data['templates'][] = array_merge($trace, array(
                'parameters' => array_map(array($this, 'varToString'), $trace['parameters']),
            ));

            $this->data['total_duration'] += $trace['duration'];
        }
    }

    /**
     * Gets the templates
     *
     * @return array
     */
    public function getTemplates()
    {
        return $this->data['templates'];
    }

    /**
     * Gets the total duration
     *
     * @return integer
     */
    public function getTotalDuration()
    {
        return $this->data['total_duration'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'templating';
    }
}
