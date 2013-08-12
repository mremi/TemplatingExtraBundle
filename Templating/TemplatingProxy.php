<?php

namespace Mremi\TemplatingExtraBundle\Templating;

use Mremi\TemplatingExtraBundle\DataCollector\TemplatingDataCollector;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * Templating proxy class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class TemplatingProxy implements EngineInterface
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var TemplateNameParser
     */
    private $templateNameParser;

    /**
     * @var TemplateLocator
     */
    private $templateLocator;

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @var TemplatingDataCollector
     */
    private $dataCollector;

    /**
     * @var array
     */
    private $trace = array();

    /**
     * Constructor
     *
     * @param EngineInterface         $templating         A templating instance
     * @param TemplateNameParser      $templateNameParser A template name parser instance
     * @param TemplateLocator         $templateLocator    A template locator instance
     * @param Stopwatch               $stopWatch          A Stopwatch instance
     * @param TemplatingDataCollector $dataCollector      A templating data collector instance
     */
    public function __construct(EngineInterface $templating, TemplateNameParser $templateNameParser, TemplateLocator $templateLocator, Stopwatch $stopWatch, TemplatingDataCollector $dataCollector)
    {
        $this->templating         = $templating;
        $this->templateNameParser = $templateNameParser;
        $this->templateLocator    = $templateLocator;
        $this->stopwatch          = $stopWatch;
        $this->dataCollector      = $dataCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function renderResponse($view, array $parameters = array(), Response $response = null)
    {
        $event = $this->startProfiling($view, $parameters);

        $response = $this->templating->renderResponse($view, $parameters, $response);

        $this->stopProfiling($event);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function render($name, array $parameters = array())
    {
        $event = $this->startProfiling($name, $parameters);

        $response = $this->templating->render($name, $parameters);

        $this->stopProfiling($event);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return $this->templating->exists($name);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $this->templating->supports($name);
    }

    /**
     * Starts the profiling
     *
     * @param mixed $name       A template
     * @param array $parameters An array of parameters passed to the template
     *
     * @return StopwatchEvent
     */
    private function startProfiling($name, array $parameters)
    {
        $templateReference = $name instanceof TemplateReferenceInterface ? $name : $this->templateNameParser->parse($name);

        $this->trace = array(
            'name'         => $templateReference->getLogicalName(),
            'file'         => $this->templateLocator->locate($templateReference),
            'parameters'   => $parameters,
            'duration'     => null,
            'memory_start' => memory_get_usage(true),
            'memory_end'   => null,
            'memory_peak'  => null,
        );

        return $this->stopwatch->start($templateReference->getLogicalName());
    }

    /**
     * Stops the profiling
     *
     * @param StopwatchEvent $event A stopwatchEvent instance
     */
    private function stopProfiling(StopwatchEvent $event)
    {
        $event->stop();

        $this->trace = array_merge($this->trace, array(
            'duration'    => $event->getDuration(),
            'memory_end'  => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
        ));

        $this->dataCollector->addTrace($this->trace);
    }
}
