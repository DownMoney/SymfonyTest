<?php
/**
 * Created by IntelliJ IDEA.
 * User: michaellotkowski
 * Date: 14/07/2016
 * Time: 16:05
 */

namespace tests\AppBundle;


use AppBundle\Mailer;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $email = 'test@example.com';
        $method = 'sendmail';

        $mailer = new Mailer($method);

        $val = $mailer->send($email, $method);

        $this->assertEquals("Sending an email to ".$email." using ".$method, $val);
    }
}