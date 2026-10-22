<?php

namespace App\Enum;

enum AnimalStatus: string
{
    case Available = 'Disponible';
    case Pending = "En cours d'adoption";
    case Adopted = 'Adopté';
}

