<?php

namespace Theodo\Bundle\Drupal8Bundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Theodo\Bundle\Drupal8Bundle\Drupal\DrupalInterface;

/**
 * Class DrupalRouterListener
 *
 * @author Thierry Marianne <thierrym@theodo.fr>
 * @author Kenny Durand <kennyd@theodo.fr>
 */
class DrupalRouterListener implements EventSubscriberInterface
{
    /**
     * @var \Theodo\Bundle\Drupal8Bundle\Drupal\DrupalInterface
     */
    protected $drupal;

    /**
     * @var RouterListener
     */
    protected $listener;

    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param DrupalInterface $drupal
     */
    public function __construct(DrupalInterface $drupal, RouterListener $listener, LoggerInterface $logger = null)
    {
        $this->drupal = $drupal;
        $this->delegate = $listener;
        $this->logger = $logger;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @return GetResponseEvent
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        try {
            $this->delegate->onKernelRequest($event);
        } catch (NotFoundHttpException $e) {
            if ($this->logger) {
                $this->logger->info('Drupal will handle the request.');
            }

            $this->drupal->initialize();

            if ($this->drupal->hasResponse()) {
                $response = $this->drupal->getResponse();
                if ($response->getStatusCode() !== 404) {
                    $event->setResponse($response);

                    return $event;
                }
            }

            $request = $event->getRequest();
            $message = sprintf('Neither Symfony nor Drupal were able to find a route for "%s %s"', $request->getMethod(), $request->getPathInfo());

            throw new NotFoundHttpException($message, $e);
        }
    }

    /**
     * @{inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 31)
        );
    }
}
