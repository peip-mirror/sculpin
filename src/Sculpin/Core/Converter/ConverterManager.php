<?php

declare(strict_types=1);

/*
 * This file is a part of Sculpin.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sculpin\Core\Converter;

use Sculpin\Core\Event\ConvertEvent;
use Sculpin\Core\Formatter\FormatterManager;
use Sculpin\Core\Sculpin;
use Sculpin\Core\Source\SourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Converter Manager.
 *
 * @author Beau Simensen <beau@dflydev.com>
 */
class ConverterManager
{
    /**
     * Event Dispatcher
     *
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Formatter Manager
     *
     * @var FormatterManager
     */
    protected $formatterManager;

    /**
     * Converters
     *
     * @var array
     */
    protected $converters = [];

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher  Event Dispatcher
     * @param FormatterManager         $formatterManager Formatter Manager
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, FormatterManager $formatterManager)
    {
        $this->formatterManager = $formatterManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->registerConverter('null', new NullConverter);
    }

    /**
     * Register converter
     *
     * @param string             $name      Name
     * @param ConverterInterface $converter Converter
     */
    public function registerConverter(string $name, ConverterInterface $converter): void
    {
        $this->converters[$name] = $converter;
    }

    /**
     * Converter
     *
     * @param string $name Name
     *
     * @return ConverterInterface
     */
    public function converter(string $name): ConverterInterface
    {
        return $this->converters[$name];
    }

    /**
     * Convert Source
     *
     * @param SourceInterface $source Source
     */
    public function convertSource(SourceInterface $source): void
    {
        $converters = $source->data()->get('converters');
        if (!$converters || !is_array($converters)) {
            $converters = ['null'];
        }

        foreach ($converters as $converter) {
            $this->eventDispatcher->dispatch(
                Sculpin::EVENT_BEFORE_CONVERT,
                new ConvertEvent($source, $converter, $this->formatterManager->defaultFormatter())
            );
            $this->converter($converter)->convert(new SourceConverterContext($source));
            $this->eventDispatcher->dispatch(
                Sculpin::EVENT_AFTER_CONVERT,
                new ConvertEvent($source, $converter, $this->formatterManager->defaultFormatter())
            );
        }
    }
}
