<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\HasLocalizedOptions;

enum ServiceType: string
{
    use HasLocalizedOptions;

    case IsoManagementTraining = 'iso_management_training';
    case HseTraining = 'hse_training';
    case GreenhouseGasCarbonTraining = 'greenhouse_gas_carbon_training';
    case EsgSustainabilityTraining = 'esg_sustainability_training';
    case SustainableSupplyChainTraining = 'sustainable_supply_chain_training';
    case SocialResponsibilityTraining = 'social_responsibility_training';
    case GreenBuildingTraining = 'green_building_training';

    case EsgConsulting = 'esg_consulting';
    case GreenhouseGasReductionConsulting = 'greenhouse_gas_reduction_consulting';
    case SbtiNetZeroConsulting = 'sbti_net_zero_consulting';
    case EnergyAuditConsulting = 'energy_audit_consulting';
    case CbamConsulting = 'cbam_consulting';
    case EprConsulting = 'epr_consulting';
    case GreenCircularEconomyConsulting = 'green_circular_economy_consulting';
    case EudrConsulting = 'eudr_consulting';
    case NoiseMapConsulting = 'noise_map_consulting';

    case SolarEnergyProject = 'solar_energy_project';
    case CarbonCreditProject = 'carbon_credit_project';
    case PlasticCreditProject = 'plastic_credit_project';
    case BiocharProject = 'biochar_project';
    case WasteCollectionRecyclingProject = 'waste_collection_recycling_project';
    case EnergySavingProject = 'energy_saving_project';
    case GreenPortProject = 'green_port_project';

    case ScientificResearch = 'scientific_research';
    case ClimateChangeAdaptationResearch = 'climate_change_adaptation_research';
    case GreenTechnologyTransfer = 'green_technology_transfer';

    case ScientificEventCommunication = 'scientific_event_communication';

    public static function translationKey(): string
    {
        return 'service_type';
    }

    public function contractType(): ContractType
    {
        return match ($this) {
            self::IsoManagementTraining,
            self::HseTraining,
            self::GreenhouseGasCarbonTraining,
            self::EsgSustainabilityTraining,
            self::SustainableSupplyChainTraining,
            self::SocialResponsibilityTraining,
            self::GreenBuildingTraining => ContractType::Training,

            self::EsgConsulting,
            self::GreenhouseGasReductionConsulting,
            self::SbtiNetZeroConsulting,
            self::EnergyAuditConsulting,
            self::CbamConsulting,
            self::EprConsulting,
            self::GreenCircularEconomyConsulting,
            self::EudrConsulting,
            self::NoiseMapConsulting => ContractType::Consulting,

            self::SolarEnergyProject,
            self::CarbonCreditProject,
            self::PlasticCreditProject,
            self::BiocharProject,
            self::WasteCollectionRecyclingProject,
            self::EnergySavingProject,
            self::GreenPortProject => ContractType::Project,

            self::ScientificResearch,
            self::ClimateChangeAdaptationResearch,
            self::GreenTechnologyTransfer => ContractType::ResearchTechnologyTransfer,

            self::ScientificEventCommunication => ContractType::ScientificEventCommunication,
        };
    }

    /**
     * @return array<string, string>
     */
    public static function optionsFor(ContractType $contractType): array
    {
        $options = [];

        foreach (self::cases() as $serviceType) {
            if ($serviceType->contractType() === $contractType) {
                $options[$serviceType->value] = $serviceType->label();
            }
        }

        return $options;
    }
}
