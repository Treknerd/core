<?php

declare(strict_types=1);

namespace Stu\Module\Spacecraft\Lib\Movement\Route;

use Mockery\MockInterface;
use Override;
use Stu\Module\PlayerSetting\Lib\UserEnum;
use Stu\Module\Spacecraft\Lib\Battle\AlertDetection\AlertReactionFacadeInterface;
use Stu\Module\Ship\Lib\Fleet\LeaveFleetInterface;
use Stu\Module\Spacecraft\Lib\Message\MessageFactoryInterface;
use Stu\Module\Spacecraft\Lib\Message\MessageInterface;
use Stu\Module\Spacecraft\Lib\Movement\Component\PreFlight\ConditionCheckResult;
use Stu\Module\Spacecraft\Lib\Movement\Component\PreFlight\PreFlightConditionsCheckInterface;
use Stu\Module\Spacecraft\Lib\Movement\ShipMovementInformationAdderInterface;
use Stu\Module\Spacecraft\Lib\Movement\ShipMover;
use Stu\Module\Spacecraft\Lib\Movement\ShipMoverInterface;
use Stu\Module\Ship\Lib\ShipWrapperInterface;
use Stu\Module\Spacecraft\Lib\Message\MessageCollectionInterface;
use Stu\Orm\Entity\MapInterface;
use Stu\Orm\Entity\ShipInterface;
use Stu\Orm\Repository\SpacecraftRepositoryInterface;
use Stu\StuTestCase;

class ShipMoverTest extends StuTestCase
{
    /** @var MockInterface&SpacecraftRepositoryInterface */
    private $spaceRepository;
    /** @var MockInterface&ShipMovementInformationAdderInterface */
    private $shipMovementInformationAdder;
    /** @var MockInterface&PreFlightConditionsCheckInterface */
    private $preFlightConditionsCheck;
    /** @var MockInterface&LeaveFleetInterface */
    private $leaveFleet;
    /** @var MockInterface&AlertReactionFacadeInterface */
    private $alertReactionFacade;
    /** @var MockInterface&MessageFactoryInterface */
    private $messageFactory;

    private ShipMoverInterface $subject;

    #[Override]
    protected function setUp(): void
    {
        $this->spaceRepository = $this->mock(SpacecraftRepositoryInterface::class);
        $this->shipMovementInformationAdder = $this->mock(ShipMovementInformationAdderInterface::class);
        $this->preFlightConditionsCheck = $this->mock(PreFlightConditionsCheckInterface::class);
        $this->leaveFleet = $this->mock(LeaveFleetInterface::class);
        $this->alertReactionFacade = $this->mock(AlertReactionFacadeInterface::class);
        $this->messageFactory = $this->mock(MessageFactoryInterface::class);

        $this->subject = new ShipMover(
            $this->spaceRepository,
            $this->shipMovementInformationAdder,
            $this->preFlightConditionsCheck,
            $this->leaveFleet,
            $this->alertReactionFacade,
            $this->messageFactory
        );
    }

    public function testCheckAndMove(): void
    {
        $ship = $this->mock(ShipInterface::class);
        $wrapper = $this->mock(ShipWrapperInterface::class);
        $flightRoute = $this->mock(FlightRouteInterface::class);
        $map = $this->mock(MapInterface::class);
        $conditionCheckResult = $this->mock(ConditionCheckResult::class);
        $messageCollection = $this->mock(MessageCollectionInterface::class);
        $failureMessage = $this->mock(MessageInterface::class);

        $ship->shouldReceive('getName')
            ->withNoArgs()
            ->once()
            ->andReturn("SHIP");
        $ship->shouldReceive('isFleetLeader')
            ->withNoArgs()
            ->once()
            ->andReturn(false);
        $ship->shouldReceive('getTractoredShip')
            ->withNoArgs()
            ->once()
            ->andReturn(null);
        $ship->shouldReceive('isDestroyed')
            ->withNoArgs()
            ->once()
            ->andReturn(false);
        $ship->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn(12345);

        $wrapper->shouldReceive('get')
            ->withNoArgs()
            ->andReturn($ship);
        $wrapper->shouldReceive('getFleetWrapper')
            ->withNoArgs()
            ->twice()
            ->andReturn(null);

        $map->shouldReceive('getFieldType->getPassable')
            ->withNoArgs()
            ->once()
            ->andReturn(true);

        $flightRoute->shouldReceive('isDestinationArrived')
            ->withNoArgs()
            ->once()
            ->andReturn(false);
        $flightRoute->shouldReceive('getNextWaypoint')
            ->withNoArgs()
            ->once()
            ->andReturn($map);
        $flightRoute->shouldReceive('abortFlight')
            ->withNoArgs()
            ->once();

        $conditionCheckResult->shouldReceive('isFlightPossible')
            ->withNoArgs()
            ->once()
            ->andReturn(false);
        $conditionCheckResult->shouldReceive('getInformations')
            ->withNoArgs()
            ->once()
            ->andReturn(['FAILURE']);


        $this->preFlightConditionsCheck->shouldReceive('checkPreconditions')
            ->with($wrapper, [12345 => $wrapper], $flightRoute, false)
            ->once()
            ->andReturn($conditionCheckResult);

        $this->messageFactory->shouldReceive('createMessageCollection')
            ->withNoArgs()
            ->once()
            ->andReturn($messageCollection);
        $messageCollection->shouldReceive('addInformation')
            ->with('Der Weiterflug wurde aus folgenden Gründen abgebrochen:')
            ->once();
        $messageCollection->shouldReceive('add')
            ->with($failureMessage)
            ->once();
        $this->messageFactory->shouldReceive('createMessage')
            ->with(UserEnum::USER_NOONE, null, ['FAILURE'])
            ->once()
            ->andReturn($failureMessage);

        $this->subject->checkAndMove($wrapper, $flightRoute);
    }
}
