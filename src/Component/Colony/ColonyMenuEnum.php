<?php

declare(strict_types=1);

namespace Stu\Component\Colony;

use Stu\Component\Building\BuildingFunctionEnum;
use Stu\Module\Building\BuildingFunctionTypeEnum;
use Stu\Module\Colony\Component\ColonyComponentEnum;
use Stu\Module\Colony\View\ShowAcademy\ShowAcademy;
use Stu\Module\Colony\View\ShowAirfield\ShowAirfield;
use Stu\Module\Colony\View\ShowBuildingManagement\ShowBuildingManagement;
use Stu\Module\Colony\View\ShowBuildMenu\ShowBuildMenu;
use Stu\Module\Colony\View\ShowBuildPlans\ShowBuildPlans;
use Stu\Module\Colony\View\ShowFighterShipyard\ShowFighterShipyard;
use Stu\Module\Colony\View\ShowMainscreen\ShowMainscreen;
use Stu\Module\Colony\View\ShowManagement\ShowManagement;
use Stu\Module\Colony\View\ShowMisc\ShowMisc;
use Stu\Module\Colony\View\ShowModuleFab\ShowModuleFab;
use Stu\Module\Colony\View\ShowShipDisassembly\ShowShipDisassembly;
use Stu\Module\Colony\View\ShowShipRepair\ShowShipRepair;
use Stu\Module\Colony\View\ShowShipRetrofit\ShowShipRetrofit;
use Stu\Module\Colony\View\ShowShipyard\ShowShipyard;
use Stu\Module\Colony\View\ShowSocial\ShowSocial;
use Stu\Module\Colony\View\ShowSubspaceTelescope\ShowSubspaceTelescope;
use Stu\Module\Colony\View\ShowTorpedoFab\ShowTorpedoFab;
use Stu\Module\Colony\View\ShowWaste\ShowWaste;

enum ColonyMenuEnum: int
{
    case MENU_MAINSCREEN = 0;
    case MENU_BUILD = 1;
    case MENU_INFO = 2;
    case MENU_OPTION = 3;
    case MENU_SOCIAL = 4;
    case MENU_BUILDINGS = 5;
    case MENU_AIRFIELD = 6;
    case MENU_MODULEFAB = 7;
    case MENU_SHIPYARD = 8;
    case MENU_BUILDPLANS = 9;
    case MENU_FIGHTER_SHIPYARD = 10;
    case MENU_TORPEDOFAB = 11;
    case MENU_ACADEMY = 12;
    case MENU_WASTE = 13;
    case MENU_FAB_HALL = 14;
    case MENU_TECH_CENTER = 15;
    case MENU_SUBSPACE_TELESCOPE = 16;
    case MENU_SHIP_DISASSEMBLY = 17;
    case MENU_SHIP_REPAIR = 18;
    case MENU_SHIP_RETROFIT = 19;

    public static function getFor(mixed $value): ColonyMenuEnum
    {
        if ($value === null) {
            return self::MENU_MAINSCREEN;
        }

        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom($value) ?? self::MENU_MAINSCREEN;
    }

    public static function getMenuClass(ColonyMenuEnum $currentMenu, int $value): string
    {
        if ($currentMenu->value === $value) {
            return 'selected';
        }

        return "";
    }


    /** @return array<BuildingFunctionEnum>|null */
    public function getNeededBuildingFunction(): ?array
    {
        return match ($this) {
            self::MENU_AIRFIELD => [BuildingFunctionEnum::AIRFIELD],
            self::MENU_MODULEFAB => BuildingFunctionTypeEnum::getModuleFabOptions(),
            self::MENU_SHIPYARD => BuildingFunctionTypeEnum::getShipyardOptions(),
            self::MENU_BUILDPLANS => BuildingFunctionTypeEnum::getShipyardOptions(),
            self::MENU_FIGHTER_SHIPYARD => [BuildingFunctionEnum::FIGHTER_SHIPYARD],
            self::MENU_TORPEDOFAB => [BuildingFunctionEnum::TORPEDO_FAB],
            self::MENU_ACADEMY => [BuildingFunctionEnum::ACADEMY],
            self::MENU_WASTE => [BuildingFunctionEnum::WAREHOUSE],
            self::MENU_FAB_HALL => [BuildingFunctionEnum::FABRICATION_HALL],
            self::MENU_TECH_CENTER => [BuildingFunctionEnum::TECH_CENTER],
            self::MENU_SUBSPACE_TELESCOPE => [BuildingFunctionEnum::SUBSPACE_TELESCOPE],
            self::MENU_SHIP_DISASSEMBLY => BuildingFunctionTypeEnum::getShipyardOptions(),
            self::MENU_SHIP_REPAIR => BuildingFunctionTypeEnum::getShipyardOptions(),
            self::MENU_SHIP_RETROFIT => BuildingFunctionTypeEnum::getShipyardOptions(),
            default => null
        };
    }

    public function getViewIdentifier(): string
    {
        return match ($this) {
            self::MENU_MAINSCREEN => ShowMainscreen::VIEW_IDENTIFIER,
            self::MENU_BUILD => ShowBuildMenu::VIEW_IDENTIFIER,
            self::MENU_INFO => ShowManagement::VIEW_IDENTIFIER,
            self::MENU_OPTION => ShowMisc::VIEW_IDENTIFIER,
            self::MENU_SOCIAL => ShowSocial::VIEW_IDENTIFIER,
            self::MENU_BUILDINGS => ShowBuildingManagement::VIEW_IDENTIFIER,
            self::MENU_AIRFIELD => ShowAirfield::VIEW_IDENTIFIER,
            self::MENU_MODULEFAB => ShowModuleFab::VIEW_IDENTIFIER,
            self::MENU_SHIPYARD => ShowShipyard::VIEW_IDENTIFIER,
            self::MENU_BUILDPLANS => ShowBuildPlans::VIEW_IDENTIFIER,
            self::MENU_FIGHTER_SHIPYARD => ShowFighterShipyard::VIEW_IDENTIFIER,
            self::MENU_TORPEDOFAB => ShowTorpedoFab::VIEW_IDENTIFIER,
            self::MENU_ACADEMY => ShowAcademy::VIEW_IDENTIFIER,
            self::MENU_WASTE => ShowWaste::VIEW_IDENTIFIER,
            self::MENU_FAB_HALL => ShowModuleFab::VIEW_IDENTIFIER,
            self::MENU_TECH_CENTER => ShowModuleFab::VIEW_IDENTIFIER,
            self::MENU_SUBSPACE_TELESCOPE => ShowSubspaceTelescope::VIEW_IDENTIFIER,
            self::MENU_SHIP_DISASSEMBLY => ShowShipDisassembly::VIEW_IDENTIFIER,
            self::MENU_SHIP_REPAIR => ShowShipRepair::VIEW_IDENTIFIER,
            self::MENU_SHIP_RETROFIT => ShowShipRetrofit::VIEW_IDENTIFIER
        };
    }

    public function getTemplate(): string
    {
        return match ($this) {
            self::MENU_MAINSCREEN => 'html/colony/menu/mainscreen.twig',
            self::MENU_BUILD => 'html/colony/menu/buildmenues.twig',
            self::MENU_INFO => 'html/colony/menu/management.twig',
            self::MENU_OPTION => 'html/colony/menu/miscellaneous.twig',
            self::MENU_SOCIAL => 'html/colony/menu/social.twig',
            self::MENU_BUILDINGS => 'html/colony/menu/buildingManagement.twig',
            self::MENU_AIRFIELD => 'html/colony/menu/airfield.twig',
            self::MENU_MODULEFAB => 'html/colony/menu/moduleFab.twig',
            self::MENU_SHIPYARD => 'html/colony/menu/shipyard.twig',
            self::MENU_BUILDPLANS => 'html/colony/menu/shipBuildplans.twig',
            self::MENU_FIGHTER_SHIPYARD => 'html/colony/menu/fighterShipyard.twig',
            self::MENU_TORPEDOFAB => 'html/colony/menu/torpedoFab.twig',
            self::MENU_ACADEMY => 'html/colony/menu/academy.twig',
            self::MENU_WASTE => 'html/colony/menu/waste.twig',
            self::MENU_FAB_HALL => 'html/colony/menu/componentFab.twig',
            self::MENU_TECH_CENTER => 'html/colony/menu/componentFab.twig',
            self::MENU_SUBSPACE_TELESCOPE => 'html/colony/menu/telescope.twig',
            self::MENU_SHIP_DISASSEMBLY => 'html/colony/menu/shipDisassembly.twig',
            self::MENU_SHIP_REPAIR => 'html/colony/menu/shipRepair.twig',
            self::MENU_SHIP_RETROFIT => 'html/colony/menu/shipRetrofit.twig'
        };
    }

    /** @return array<ColonyComponentEnum> */
    public function getNecessaryGuiComponents(): array
    {
        return match ($this) {
            // mainscreen
            self::MENU_MAINSCREEN => [
                ColonyComponentEnum::SURFACE,
                ColonyComponentEnum::SHIELDING,
                ColonyComponentEnum::EPS_BAR,
                ColonyComponentEnum::STORAGE,
                ColonyComponentEnum::MANAGEMENT,
                ColonyComponentEnum::EFFECTS
            ],

            // submenues
            self::MENU_BUILD => [ColonyComponentEnum::BUILD_MENUES],
            self::MENU_INFO => [ColonyComponentEnum::MANAGEMENT, ColonyComponentEnum::EFFECTS],
            self::MENU_OPTION => [],
            self::MENU_SOCIAL => [ColonyComponentEnum::SOCIAL],
            self::MENU_BUILDINGS => [ColonyComponentEnum::BUILDING_MANAGEMENT],

            // menues without surface
            self::MENU_AIRFIELD => [ColonyComponentEnum::AIRFIELD],
            self::MENU_MODULEFAB,
            self::MENU_FAB_HALL,
            self::MENU_TECH_CENTER,
            self::MENU_SHIPYARD => [ColonyComponentEnum::SHIPYARD],
            self::MENU_BUILDPLANS => [ColonyComponentEnum::SHIP_BUILDPLANS],
            self::MENU_FIGHTER_SHIPYARD => [ColonyComponentEnum::FIGHTER_SHIPYARD],
            self::MENU_TORPEDOFAB => [ColonyComponentEnum::TORPEDO_FAB],
            self::MENU_ACADEMY => [ColonyComponentEnum::ACADEMY],
            self::MENU_WASTE => [],
            self::MENU_SUBSPACE_TELESCOPE => [],
            self::MENU_SHIP_DISASSEMBLY => [ColonyComponentEnum::SHIP_DISASSEMBLY],
            self::MENU_SHIP_REPAIR => [ColonyComponentEnum::SHIP_REPAIR],
            self::MENU_SHIP_RETROFIT => [ColonyComponentEnum::SHIP_RETROFIT]
        };
    }
}
