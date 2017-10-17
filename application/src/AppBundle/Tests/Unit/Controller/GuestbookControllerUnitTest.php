<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Guestbook;
use AppBundle\Repository\GuestbookRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\GuestbookController;

/**
 * Class GuestbookControllerUnitTest
 * @package AppBundle\Tests\Controller
 *
 * @codeCoverageIgnore
 */
class GuestbookControllerUnitTest extends TestCase
{
    /**
     * @var GuestbookController|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $guestbookController;

    /**
     *
     */
    protected function setUp()
    {
        $this->guestbookController = $this->getMockBuilder(GuestbookController::class)
            ->setMethods(['render'])
            ->getMock();
        $this->guestbookController->setContainer($this->getContainerMockWithDoctrineMock());
    }

    /**
     *
     */
    public function testIndexAction()
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getUser')->willReturn('user1');

        $this->guestbookController->expects($this->once())->method('render')->with(
            'guestbook/index.html.twig',
            [
                'user'       => 'user1',
                'guestbooks' => [$this->getGuestbookMock()],
            ]
        );

        $callback = $this->guestbookController->indexAction($requestMock);
    }

    /**
     *
     */
    public function newActionTest()
    {

    }

    /**
     *
     */
    protected function tearDown()
    {
        $this->guestbookController = null;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getContainerMockWithDoctrineMock()
    {
        $conMock = $this->createMock(Container::class);

        $conMock->method('get')
            ->with('doctrine')
            ->willReturn($this->getDoctrineServiceMock());

        return $conMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDoctrineServiceMock()
    {
        $docMock = $this->createMock(Registry::class);
        $docMock->method('getManager')->willReturn($this->getObjectManagerMock());

        return $docMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getObjectManagerMock()
    {
        $ormMock = $this->createMock(ObjectManager::class);
        $ormMock->method('getRepository')->willReturn($this->getRepositoryMock());

        return $ormMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRepositoryMock()
    {
        $repoMock = $this->createMock(GuestbookRepository::class);
        $repoMock->method('findAll')
                 ->willReturn([$this->getGuestbookMock()]);

        return $repoMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getGuestbookMock()
    {
        $gbMock = $this->createMock(Guestbook::class);
        $gbMock->method('getId')
               ->willReturn(1);

        $gbMock->method('getUser')
               ->willReturn('user1');

        $gbMock->method('getMessage')
               ->willReturn('message1');

        $gbMock->method('getDate')
               ->willReturn(new \DateTime());


        return $gbMock;
    }
}
