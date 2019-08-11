<?php

namespace Anper\Mailer\Tests\Subscriber;

use Anper\Mailer\Event\AfterFetchEvent;
use Anper\Mailer\Event\BeforeFetchEvent;
use Anper\Mailer\Subscriber\Defaults;
use PHPUnit\Framework\TestCase;

class DefaultsTest extends TestCase
{
    public function testSetAndGetDefaults()
    {
        $dd = new Defaults();

        $defaults = ['subject' => 'hello'];

        $dd->setDefaults($defaults);

        $this->assertEquals($defaults, $dd->getDefaults());
    }

    public function testSetDefaultsFromConstructor()
    {
        $defaults = ['subject' => 'hello'];
        $dd = new Defaults($defaults);

        $this->assertEquals($defaults, $dd->getDefaults());
    }

    public function testAddDefaults()
    {
        $dd = new Defaults();
        $dd->addDefault('subject', 'hello');

        $this->assertEquals(['subject' => 'hello'], $dd->getDefaults());
    }

    public function testSetAndGetContext()
    {
        $dd = new Defaults();

        $context = ['subject' => 'hello'];

        $dd->setContext($context);

        $this->assertEquals($context, $dd->getContext());
    }

    public function testSetContextFromConstructor()
    {
        $context = ['subject' => 'hello'];
        $dd = new Defaults([], $context);

        $this->assertEquals($context, $dd->getContext());
    }

    public function testAddContext()
    {
        $dd = new Defaults();
        $dd->addContext('subject', 'hello');

        $this->assertEquals(['subject' => 'hello'], $dd->getContext());
    }

    public function testOnBeforeFetch()
    {
        $event = new BeforeFetchEvent('test', ['subject' => 'hello']);

        $dd = new Defaults();
        $dd->addContext('body', 'world');

        $dd->onBeforeFetch($event);

        $this->assertEquals($event->getArguments(), [
            'subject' => 'hello',
            'body' => 'world',
        ]);
    }

    public function testOnAfterFetch()
    {
        $event = new AfterFetchEvent('test', ['subject' => 'hello']);

        $dd = new Defaults();
        $dd->addDefault('body', 'world');

        $dd->onAfterFetch($event);

        $this->assertEquals($event->getArguments(), [
            'subject' => 'hello',
            'body' => 'world',
        ]);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals(Defaults::getSubscribedEvents(), [
            BeforeFetchEvent::class => 'onBeforeFetch',
            AfterFetchEvent::class => 'onAfterFetch',
        ]);
    }
}
