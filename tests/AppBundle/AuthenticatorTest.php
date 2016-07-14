<?php
/**
 * Created by IntelliJ IDEA.
 * User: michaellotkowski
 * Date: 14/07/2016
 * Time: 16:01
 */

namespace tests\AppBundle;


use AppBundle\Authenticator;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;

class AuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterOnlyAllowValidUser()
    {
        $user = new User();

        $registryMock = $this->getMockBuilder('ManagerRegistry')->setMethods(['persist', 'flush'])->getMock();
        $registryMock->expects($this->once())->method('persist');
        $registryMock->expects($this->once())->method('flush');

        $encoderMock= $this->getMockBuilder('Encoder')->setMethods(['encodePassword'])->getMock();
        $encoderMock->expects($this->once())->method('encodePassword')->will($this->returnValue('PASSWORD HASH'));

        $newUser = Authenticator::register($user, $registryMock, $encoderMock);

        $this->assertEquals('PASSWORD HASH', $newUser->getPassword());
    }

    public function testLoginOnlyAllowValidUsers()
    {
        $user = new User();
        $user->setEmail('username');

        $storeMock = $this->getMockBuilder('Store')->setMethods(['findOneByEmail'])->getMock();
        $storeMock->expects($this->once())->method('findOneByEmail')->will($this->returnValue($user));

        $registryMock = $this->getMockBuilder('ManagerRegistry')->setMethods(['getRepository'])->getMock();
        $registryMock->expects($this->once())->method('getRepository')->will($this->returnValue($storeMock));


        $encoderMock = $this->getMockBuilder('Encoder')->setMethods(['isPasswordValid'])->getMock();
        $encoderMock->expects($this->once())->method('isPasswordValid')->will($this->returnCallback([$this, 'loginCallback']));


        $val = Authenticator::login($registryMock, 'username', 'password', $encoderMock);

        $this->assertNotNull($val);
    }

    public function testLoginFailIncorrectCredentials()
    {
        $user = new User();
        $user->setEmail('username');

        $storeMock = $this->getMockBuilder('Store')->setMethods(['findOneByEmail'])->getMock();
        $storeMock->expects($this->once())->method('findOneByEmail')->will($this->returnValue($user));

        $registryMock = $this->getMockBuilder('ManagerRegistry')->setMethods(['getRepository'])->getMock();
        $registryMock->expects($this->once())->method('getRepository')->will($this->returnValue($storeMock));


        $encoderMock = $this->getMockBuilder('Encoder')->setMethods(['isPasswordValid'])->getMock();
        $encoderMock->expects($this->once())->method('isPasswordValid')->will($this->returnCallback([$this, 'loginCallback']));


        $val = Authenticator::login($registryMock, 'WRONG', 'WRONG', $encoderMock);

        $this->assertNull($val);
    }

    public function testLoginHandleWrongUsername()
    {
        $storeMock = $this->getMockBuilder('Store')->setMethods(['findOneByEmail'])->getMock();
        $storeMock->expects($this->once())->method('findOneByEmail')->will($this->returnValue(null));

        $registryMock = $this->getMockBuilder('ManagerRegistry')->setMethods(['getRepository'])->getMock();
        $registryMock->expects($this->once())->method('getRepository')->will($this->returnValue($storeMock));


        $encoderMock = $this->getMockBuilder('Encoder')->setMethods(['isPasswordValid'])->getMock();
        $encoderMock->expects($this->never())->method('isPasswordValid')->will($this->returnCallback([$this, 'loginCallback']));


        $val = Authenticator::login($registryMock, 'WRONG', 'WRONG', $encoderMock);

        $this->assertNull($val);
    }

    function loginCallback()
    {
        $args = func_get_args();

        return $args[0]->getUsername() === 'username' && $args[1] === 'password';
    }
}
