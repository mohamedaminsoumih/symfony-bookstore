<?php

namespace App\Provider;

use Faker\Provider\Base;

class Auteur extends Base
{
    private $sexe = array('M', 'F');

    public function sexe(): string
    {
        return static::randomElement($this->sexe);
    }
}