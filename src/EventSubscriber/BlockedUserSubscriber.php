<?php

/**
 * Blocked user subscriber.
 */

namespace App\EventSubscriber;

use App\Entity\Enum\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class BlockedUserSubscriber.
 */
class BlockedUserSubscriber implements EventSubscriberInterface
{
    /**
     * Constructor.
     *
     * @param Security              $security
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(private readonly Security $security, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');
        $allowedRoutes = ['user_blocked', 'app_logout'];

        if (in_array($currentRoute, $allowedRoutes, true)) {
            return;
        }

        if ($this->security->isGranted(UserRole::ROLE_BLOCKED->value)) {
            $url = $this->urlGenerator->generate('user_blocked');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
