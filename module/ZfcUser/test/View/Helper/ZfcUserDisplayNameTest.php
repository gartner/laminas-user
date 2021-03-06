<?php

namespace ZfcUserTest\View\Helper;

use Laminas\Authentication\AuthenticationService;
use PHPUnit\Framework\TestCase;
use ZfcUser\Entity\User;
use ZfcUser\Exception\DomainException;
use ZfcUser\View\Helper\ZfcUserDisplayName as ViewHelper;
use stdClass;

class ZfcUserDisplayNameTest extends TestCase
{
    protected $helper;

    protected $authService;

    protected $user;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $helper = new ViewHelper();
        $this->helper = $helper;

        $authService = $this->getMockBuilder(AuthenticationService::class)
            ->getMock();
        $this->authService = $authService;

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $this->user = $user;

        $helper->setAuthService($authService);
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->user);
        unset($this->authService);
        unset($this->helper);
    }

    /**
     * @covers ZfcUser\View\Helper\ZfcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserAndNotLoggedIn(): void
    {
        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));

        $result = $this->helper->__invoke(null);

        $this->assertFalse($result);
    }

    /**
     * @covers ZfcUser\View\Helper\ZfcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithWrongUserObject(): void
    {
        $this->expectException(DomainException::class);

        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));
        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue(new stdClass()));

        $this->helper->__invoke(null);
    }

    /**
     * @covers ZfcUser\View\Helper\ZfcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithDisplayName(): void
    {
        $this->user->expects($this->once())
            ->method('getDisplayName')
            ->will($this->returnValue('zfcUser'));

        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));
        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($this->user));

        $result = $this->helper->__invoke(null);

        $this->assertEquals('zfcUser', $result);
    }

    /**
     * @covers ZfcUser\View\Helper\ZfcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithoutDisplayNameButWithUsername(): void
    {
        $this->user->expects($this->once())
            ->method('getDisplayName')
            ->will($this->returnValue(null));
        $this->user->expects($this->once())
            ->method('getUsername')
            ->will($this->returnValue('zfcUser'));

        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));
        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($this->user));

        $result = $this->helper->__invoke(null);

        $this->assertEquals('zfcUser', $result);
    }

    /**
     * @covers ZfcUser\View\Helper\ZfcUserDisplayName::__invoke
     */
    public function testInvokeWithoutUserButLoggedInWithoutDisplayNameAndWithOutUsernameButWithEmail(): void
    {
        $this->user->expects($this->once())
            ->method('getDisplayName')
            ->will($this->returnValue(null));
        $this->user->expects($this->once())
            ->method('getUsername')
            ->will($this->returnValue(null));
        $this->user->expects($this->once())
            ->method('getEmail')
            ->will($this->returnValue('zfcUser@zfcUser.com'));

        $this->authService->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));
        $this->authService->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($this->user));

        $result = $this->helper->__invoke(null);

        $this->assertEquals('zfcUser', $result);
    }

    /**
     * @covers ZfcUser\View\Helper\ZfcUserDisplayName::setAuthService
     * @covers ZfcUser\View\Helper\ZfcUserDisplayName::getAuthService
     */
    public function testSetGetAuthService(): void
    {
        // We set the authservice in setUp, so we dont have to set it again
        $this->assertSame($this->authService, $this->helper->getAuthService());
    }
}
