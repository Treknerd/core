<?php

declare(strict_types=1);

namespace Stu\Module\Spacecraft\Lib\Movement\Component\PreFlight;

use Mockery\MockInterface;
use Override;
use Stu\Module\Ship\Lib\Fleet\LeaveFleetInterface;
use Stu\Module\Ship\Lib\ShipWrapperInterface;
use Stu\Orm\Entity\FleetInterface;
use Stu\Orm\Entity\ShipInterface;
use Stu\StuTestCase;

class ConditionCheckResultTest extends StuTestCase
{
    /** @var MockInterface&LeaveFleetInterface */
    private MockInterface $leaveFleet;

    /** @var MockInterface&ShipWrapperInterface */
    private MockInterface $leader;

    #[Override]
    protected function setUp(): void
    {
        $this->leaveFleet = $this->mock(LeaveFleetInterface::class);
        $this->leader = $this->mock(ShipWrapperInterface::class);
    }

    public function testAddBlockedShipExpectBlockWhenFixedFleet(): void
    {
        $ship = $this->mock(ShipInterface::class);

        $ship->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(42);
        $ship->shouldReceive('isFleetLeader')
            ->withNoArgs()
            ->once()
            ->andReturn(false);
        $ship->shouldReceive('getFleet')
            ->withNoArgs()
            ->once()
            ->andReturn($this->mock(FleetInterface::class));

        $this->leader->shouldReceive('get')
            ->withNoArgs()
            ->once()
            ->andReturn($this->mock(ShipInterface::class));

        $subject = new ConditionCheckResult($this->leaveFleet, $this->leader, true);

        $subject->addBlockedShip($ship, 'REASON');

        $this->assertTrue($subject->isBlocked($ship));
        $this->assertFalse($subject->isFlightPossible());
        $this->assertEquals(['REASON'], $subject->getInformations());
    }

    public function testAddBlockedShipExpectLeaveFleetWhenNotFixedFleet(): void
    {
        $ship = $this->mock(ShipInterface::class);

        $ship->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(42);
        $ship->shouldReceive('isFleetLeader')
            ->withNoArgs()
            ->once()
            ->andReturn(false);
        $ship->shouldReceive('getFleet')
            ->withNoArgs()
            ->once()
            ->andReturn($this->mock(FleetInterface::class));
        $ship->shouldReceive('getName')
            ->withNoArgs()
            ->once()
            ->andReturn('SHIP');
        $ship->shouldReceive('getPosX')
            ->withNoArgs()
            ->once()
            ->andReturn(22);
        $ship->shouldReceive('getPosY')
            ->withNoArgs()
            ->once()
            ->andReturn(33);

        $this->leader->shouldReceive('get')
            ->withNoArgs()
            ->andReturn($this->mock(ShipInterface::class));

        $this->leaveFleet->shouldReceive('leaveFleet')
            ->with($ship)
            ->once();

        $subject = new ConditionCheckResult($this->leaveFleet, $this->leader, false);

        $this->assertFalse($subject->isBlocked($ship));

        $subject->addBlockedShip($ship, 'REASON');
        $subject->addBlockedShip($ship, 'REASON');

        $this->assertTrue($subject->isBlocked($ship));
        $this->assertTrue($subject->isFlightPossible());
        $this->assertEquals(['REASON', 'Die SHIP hat die Flotte verlassen (22|33)'], $subject->getInformations());
    }

    public function testAddBlockedShipExpectNoLeaveFleetWhenNotFixedFleetButLeaderBlocked(): void
    {
        $leaderShip = $this->mock(ShipInterface::class);
        $ship = $this->mock(ShipInterface::class);

        $leaderShip->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(55);
        $leaderShip->shouldReceive('isFleetLeader')
            ->withNoArgs()
            ->once()
            ->andReturn(true);

        $ship->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(42);
        $ship->shouldReceive('isFleetLeader')
            ->withNoArgs()
            ->once()
            ->andReturn(false);
        $ship->shouldReceive('getFleet')
            ->withNoArgs()
            ->once()
            ->andReturn($this->mock(FleetInterface::class));

        $this->leader->shouldReceive('get')
            ->withNoArgs()
            ->once()
            ->andReturn($leaderShip);

        $subject = new ConditionCheckResult($this->leaveFleet, $this->leader, false);

        $this->assertFalse($subject->isBlocked($ship));

        $subject->addBlockedShip($leaderShip, 'LEADER_REASON');
        $subject->addBlockedShip($ship, 'REASON');
        $subject->addBlockedShip($ship, 'REASON');

        $this->assertTrue($subject->isBlocked($ship));
        $this->assertFalse($subject->isFlightPossible());
        $this->assertEquals(['LEADER_REASON', 'REASON'], $subject->getInformations());
    }

    public function testAddBlockedShipExpectNoLeaveFleetWhenLeader(): void
    {
        $ship = $this->mock(ShipInterface::class);

        $ship->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(42);
        $ship->shouldReceive('isFleetLeader')
            ->withNoArgs()
            ->once()
            ->andReturn(false);
        $ship->shouldReceive('getFleet')
            ->withNoArgs()
            ->once()
            ->andReturn($this->mock(FleetInterface::class));

        $this->leader->shouldReceive('get')
            ->withNoArgs()
            ->once()
            ->andReturn($ship);

        $subject = new ConditionCheckResult($this->leaveFleet, $this->leader, false);

        $this->assertFalse($subject->isBlocked($ship));

        $subject->addBlockedShip($ship, 'REASON');
        $subject->addBlockedShip($ship, 'REASON');

        $this->assertTrue($subject->isBlocked($ship));
        $this->assertFalse($subject->isFlightPossible());
        $this->assertEquals(['REASON'], $subject->getInformations());
    }

    public function testAddBlockedShipExpectBlockWhenFleetLeader(): void
    {
        $ship = $this->mock(ShipInterface::class);

        $ship->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(42);
        $ship->shouldReceive('isFleetLeader')
            ->withNoArgs()
            ->once()
            ->andReturn(true);

        $subject = new ConditionCheckResult($this->leaveFleet, $this->leader, false);

        $this->assertFalse($subject->isBlocked($ship));

        $subject->addBlockedShip($ship, 'REASON');

        $this->assertTrue($subject->isBlocked($ship));
        $this->assertFalse($subject->isFlightPossible());
        $this->assertEquals(['REASON'], $subject->getInformations());
    }

    public function testAddBlockedShipExpectBlockWhenSingleShip(): void
    {
        $ship = $this->mock(ShipInterface::class);

        $ship->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(42);
        $ship->shouldReceive('isFleetLeader')
            ->withNoArgs()
            ->once()
            ->andReturn(false);
        $ship->shouldReceive('getFleet')
            ->withNoArgs()
            ->once()
            ->andReturn(null);

        $this->leader->shouldReceive('get')
            ->withNoArgs()
            ->andReturn($ship);

        $subject = new ConditionCheckResult($this->leaveFleet, $this->leader, false);

        $this->assertFalse($subject->isBlocked($ship));

        $subject->addBlockedShip($ship, 'REASON');

        $this->assertTrue($subject->isBlocked($ship));
        $this->assertFalse($subject->isFlightPossible());
        $this->assertEquals(['REASON'], $subject->getInformations());
    }

    public function testAddBlockedShipExpectBlockWhenSingleShipIsNotFleetLeader(): void
    {
        $ship = $this->mock(ShipInterface::class);
        $fleet = $this->mock(FleetInterface::class);

        $ship->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(42);
        $ship->shouldReceive('isFleetLeader')
            ->withNoArgs()
            ->once()
            ->andReturn(false);
        $ship->shouldReceive('getFleet')
            ->withNoArgs()
            ->once()
            ->andReturn($fleet);

        $this->leader->shouldReceive('get')
            ->withNoArgs()
            ->andReturn($ship);

        $subject = new ConditionCheckResult($this->leaveFleet, $this->leader, false);

        $this->assertFalse($subject->isBlocked($ship));

        $subject->addBlockedShip($ship, 'REASON');

        $this->assertTrue($subject->isBlocked($ship));
        $this->assertFalse($subject->isFlightPossible());
        $this->assertEquals(['REASON'], $subject->getInformations());
    }
}
