<?php

namespace AppBundle\Controller;

use AppBundle\Authenticator;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/hello", name="helloworld")
     */
    public function helloAction(Request $request)
    {
        return $this->render('default/hello.html.twig');
    }

    /**
     * @Route("/send", name="send")
     */
    public function sendEmailServiceAction(Request $request)
    {
        $mailer = $this->get('app.mailer');

        $res = $mailer->send($request->query->get('email'), "");

        return $this->render('default/send.html.twig', [
            'text' => $res
        ]);
    }


    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class)
            ->add('email', TextType::class)
            ->add('password', PasswordType::class)
            ->add('save', SubmitType::class, array('label' => 'Sign up'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $encoder = $this->container->get('security.password_encoder');

            Authenticator::register($user, $em, $encoder);
        }

        return $this->render('default/form.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class)
            ->add('password', PasswordType::class)
            ->add('save', SubmitType::class, array('label' => 'Login'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $encoder = $this->container->get('security.password_encoder');

            $token = Authenticator::login($em, $user->getEmail(), $user->getPassword(), $encoder);

            if ($token) {

                $this->get("security.token_storage")->setToken($token);
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

                return $this->redirectToRoute('user');
            }

        }
        return $this->render('default/form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/user", name="user")
     */
    public function userAction(Request $request)
    {
        if ($this->get("security.token_storage")->getToken()->getUser() !== "anon.") {
            $user = $this->get("security.token_storage")->getToken()->getUser();
            var_dump($user);

            return new Response('<html><body>You have been authenticated as  ' . $user->getUsername() . '!</body></html>');
        } else {
            return $this->redirectToRoute('login');

        }
    }
}
