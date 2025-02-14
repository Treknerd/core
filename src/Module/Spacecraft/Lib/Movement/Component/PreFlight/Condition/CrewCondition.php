<?php

declare(strict_types=1);

namespace Stu\Module\Spacecraft\Lib\Movement\Component\PreFlight\Condition;

use Override;
use Stu\Module\Spacecraft\Lib\Movement\Component\PreFlight\ConditionCheckResult;
use Stu\Module\Spacecraft\Lib\Movement\Route\FlightRouteInterface;
use Stu\Module\Spacecraft\Lib\SpacecraftWrapperInterface;

class CrewCondition implements PreFlightConditionInterface
{
    #[Override]
    public function check(
        SpacecraftWrapperInterface $wrapper,
        FlightRouteInterface $flightRoute,
        ConditionCheckResult $conditionCheckResult
    ): void {
        $ship = $wrapper->get();

        if (!$ship->hasEnoughCrew()) {
            $conditionCheckResult->addBlockedShip(
                $ship,
                sprintf(
                    'Die %s hat ungenügend Crew',
                    $ship->getName()
                )
            );
        }
    }
}
