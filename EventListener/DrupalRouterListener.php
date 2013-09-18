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
use Theodo\Bundle\Drupal8Bundle\Drupal\DrupalWrapperInterface;

/**
 * Class DrupalRouterListener
 *
 * @author Thierry Marianne <thierrym@theodo.fr>
 * @author Kenny Durand <kennyd@theodo.fr>
 * @author Fabrice Bernhard <fabriceb@theodo.fr>
 */
class DrupalRouterListener implements EventSubscriberInterface
{
    /**
     * @var \Theodo\Bundle\Drupal8Bundle\Drupal\DrupalWrapperInterface
     */
    protected $drupalWrapper;

    /**
     * @var RouterListener
     */
    protected $routerListener;

    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param DrupalInterface $drupal
     */
    public function __construct(DrupalWrapperInterface $drupalWrapper, RouterListener $routerListener, LoggerInterface $logger = null)
    {
        $this->drupalWrapper = $drupalWrapper;
        $this->routerListener = $routerListener;
        $this->logger = $logger;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @return bool
     *
     */
    public function isRequestMatchingSymfonyRoute($event)
    {
        try {
            $this->routerListener->onKernelRequest($event);
        } catch (NotFoundHttpException $e) {

            return false;
        }

        return true;
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

        if (!$this->isRequestMatchingSymfonyRoute($event)) {
            if ($this->logger) {
                $this->logger->info('Drupal will handle the request.');
            }

            $drupalResponse = $this->drupalWrapper->handleRequest($event->getRequest());
            if ($drupalResponse->getStatusCode() !== 404) {
                $event->setResponse($drupalResponse);

                return $event;
            }
        }
    }

    /**
     * @{inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 33)),
        );
    }
}
