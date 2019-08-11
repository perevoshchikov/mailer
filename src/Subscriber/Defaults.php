<?php

namespace Anper\Mailer\Subscriber;

use Anper\Mailer\Event\AfterFetchEvent;
use Anper\Mailer\Event\BeforeFetchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Defaults implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var array
     */
    protected $context = [];

    /**
     * @param array $defaults Default message parameters.
     * @param array $context  Default storage context.
     */
    public function __construct(array $defaults = [], array $context = [])
    {
        $this->setDefaults($defaults);
        $this->setContext($context);
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * @param array $defaults
     *
     * @return Defaults
     */
    public function setDefaults(array $defaults): self
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return Defaults
     */
    public function addDefault(string $key, $value): self
    {
        $this->defaults[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     *
     * @return Defaults
     */
    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return Defaults
     */
    public function addContext(string $key, $value): self
    {
        $this->context[$key] = $value;

        return $this;
    }

    /**
     * @param BeforeFetchEvent $event
     */
    public function onBeforeFetch(BeforeFetchEvent $event): void
    {
        $context = \array_merge($this->getContext(), $event->getArguments());

        $event->setArguments($context);
    }

    /**
     * @param AfterFetchEvent $event
     */
    public function onAfterFetch(AfterFetchEvent $event): void
    {
        $data = \array_merge($this->getDefaults(), $event->getArguments());

        $event->setArguments($data);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeFetchEvent::class => 'onBeforeFetch',
            AfterFetchEvent::class => 'onAfterFetch',
        ];
    }
}
