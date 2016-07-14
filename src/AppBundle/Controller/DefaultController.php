<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
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
}
