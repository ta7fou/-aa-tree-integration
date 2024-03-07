<?php
namespace App\Security;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RedirectAdminAccessListener
{
    private $security;
    private $router;

    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getPathInfo(), '/admin') === 0) {
            $user = $this->security->getUser();
            if (!$user) {
                // Redirect to the admin login page if not logged in
                $loginUrl = $this->router->generate('admin_login');
                $event->setResponse(new RedirectResponse($loginUrl));
            } elseif (!$this->security->isGranted("ROLE_ADMIN")) {
                // Redirect to a "not authorized" page if not admin
                $notAuthorizedUrl = $this->router->generate('not_authorized');
                $event->setResponse(new RedirectResponse($notAuthorizedUrl));
            }
        }
    }
}
