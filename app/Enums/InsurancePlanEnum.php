<?php 

namespace App\Enums;

enum InsurancePlanEnum: string
{
    case INELIGIBLE = 'inelegivel';
    case ECONOMIC = 'economico';
    case STANDARD = 'padrao';
    case ADVANCED = 'avancado';
}