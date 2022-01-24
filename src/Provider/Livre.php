<?php

namespace App\Provider;

use Faker\Provider\Base;

class Livre extends Base
{
    private $genres = array(
        'Comedie', 'Action', 'Aventure', 'Horreur', 'Musique', 'Drama', 'Romance', 'Animation',
        'Science Fiction', 'Documentaire', 'Famille'
    );

    public function genre(): string
    {
        return static::randomElement($this->genres);
    }
}