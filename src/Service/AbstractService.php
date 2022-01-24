<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }
}
