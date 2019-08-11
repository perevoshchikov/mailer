<?php

namespace Anper\Mailer\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @method string getSubject() Return message id
 */
class BeforeFetchEvent extends GenericEvent
{
}
