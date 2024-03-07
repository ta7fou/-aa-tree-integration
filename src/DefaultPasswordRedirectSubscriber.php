<?php

// DefaultPasswordRedirectSubscriber
namespace App;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DefaultPasswordRedirectSubscriber implements EventSubscriberInterface
{
    private $tokenStorage;
    private $urlGenerator;
    private $authorizationChecker;
    private $hasher;
    private $defaultAdminPassword;

    public function __construct(TokenStorageInterface $tokenStorage, UserPasswordHasherInterface $userPasswordHasher, UrlGeneratorInterface $urlGenerator, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->urlGenerator = $urlGenerator;
        $this->authorizationChecker = $authorizationChecker;
        $this->hasher = $userPasswordHasher;
        $this->defaultAdminPassword =$_ENV['PASSWORD_ADMIN_Default'];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $token = $this->tokenStorage->getToken();

        if (!$token || $token instanceof AnonymousToken || !$this->authorizationChecker->isGranted("ROLE_ADMIN")) {
            return; // Early return if not authenticated or not an admin
        }

        $user = $token->getUser();

        if ($user && $this->isUsingDefaultPassword($user)) {
            $response = new RedirectResponse($this->urlGenerator->generate('app_profile_back'));
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }


    private function isUsingDefaultPassword(User $user): bool
    {
        $hashedDefaultPassword = $this->hasher->hashPassword($user, $this->defaultAdminPassword);
        return $user->getPassword() === $hashedDefaultPassword;
    }
}
