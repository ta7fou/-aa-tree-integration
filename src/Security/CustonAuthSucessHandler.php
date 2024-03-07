<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Routing\RouterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class CustonAuthSucessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;
    private $authorizationChecker;
    private $logger;
    private $hasher;
    private $defaultAdminPassword;

    public function __construct(
        RouterInterface $router,
        AuthorizationCheckerInterface $authorizationChecker,
        UserPasswordHasherInterface $userPasswordHasher,
        LoggerInterface $logger
    ) {
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
        $this->logger = $logger;
        $this->hasher = $userPasswordHasher;
        $this->defaultAdminPassword = $_ENV['PASSWORD_ADMIN_Default'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();


        // Check if the user has the default password and is an admin
        if ($this->isUsingDefaultPassword($user) && $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            // Log redirection to app_profile_back
            $this->logger->info('Redirecting to app_profile_back');
            return new RedirectResponse($this->router->generate('app_profile_back'));
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $this->logger->info('Redirecting to app_profile_back');
            return new RedirectResponse($this->router->generate('app_home_back'));
        }


        $this->logger->info('Redirecting to app_home');
        return new RedirectResponse($this->router->generate('app_home'));
    }

    private function isUsingDefaultPassword(User $user): bool
    {
        $hashedDefaultPassword = $this->hasher->hashPassword($user, $this->defaultAdminPassword);
        return $user->getPassword() === $hashedDefaultPassword;
    }
}