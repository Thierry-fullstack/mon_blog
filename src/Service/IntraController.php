<?php

namespace App\Service;

use App\Entity\User;
use App\Message\SendActivationMessage;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IntraController extends AbstractController
{
    private const string WEBMASTER = 'webmaster@my-domain.org';
    private const string CHECK_YOUR_IDENTITY = 'check your identity';  // subject
    private const string VERIFICATION = 'verification'; // template
    /**
     *
     */
    private const string SUBJECT = 'Activate your account'; // subject
    private const string CHECk_USER = 'check_user'; // method
    private const string REGISTER = 'register'; // template

    static function userVerified(User $user):bool
    {
            if($user->isVerified()===false){
                return 1;
        }
        return 0;
    }

    static function userCompleted(User $user):bool
    {
        if($user->isVerified()===true && $user->isCompleted()===false){
            return 1;
        }
        return 0;
    }
    static function userLogged(User $user):bool
    {
        if( $user->isVerified() && $user->isCompleted() && !($user->isLogged())){
            return 1;
        }
    return 0;
    }
    static function userHacked(User $user):bool
    {
        if(!$user->isLogged()){
            return 1;
    }
    return false;
    }

    /**
     * email validation function
     * @param User $user
     * @param JwtService $jwt
     * @param MessageBusInterface $messageBus
     * @return void
     * @throws ExceptionInterface
     */
    public function emailValidate(User $user,JwtService $jwt ,MessageBusInterface $messageBus ): void
    {
        $header = ['typ' => 'JWT', 'alg' => 'HS256'];
        $payload = ['user_id' => $user->getId()];
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
        $url = $this->generateUrl(self::CHECk_USER, ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
        $messageBus->dispatch(new SendActivationMessage( self::WEBMASTER, $user->getEmail(), self::SUBJECT, self::REGISTER, ['user' => $user, 'url' => $url]));
    }

    /**
     * @param User $user
     * @param MessageBusInterface $messageBus
     * @param array $context
     * @return void
     * @throws ExceptionInterface
     */
    public function emailSimple(User $user, MessageBusInterface $messageBus, array $context):void
    {
        $messageBus->dispatch(new SendActivationMessage(self::WEBMASTER,$user->getEmail(),self::CHECK_YOUR_IDENTITY,self::VERIFICATION,$context));
    }

}
