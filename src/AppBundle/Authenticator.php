<?php

namespace AppBundle;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class Authenticator
{
    public static function register(User $user, $registry, $encoder)
    {
        $encoded = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encoded);
        $registry->persist($user);
        $registry->flush();

        return $user;
    }

    public static function login($registry, $email, $password, $encoder)
    {
        $actualUser = $registry
            ->getRepository('AppBundle:User')
            ->findOneByEmail($email);

        if ($actualUser) {
            $validPassword = $encoder->isPasswordValid(
                $actualUser,
                $password
            );

            if ($validPassword) {
                $token = new UsernamePasswordToken($actualUser, $actualUser->getPassword(), "main", $actualUser->getRoles());
                return $token;
            }

            return null;
        } else {
            return null;
        }
    }
}