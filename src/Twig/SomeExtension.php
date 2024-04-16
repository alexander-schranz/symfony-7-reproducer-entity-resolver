<?php

namespace App\Twig;

use App\Entity\SomeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class SomeExtension extends \Twig\Extension\AbstractExtension
{
    private EntityRepository $someRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
         $this->someRepository = $entityManager->getRepository(SomeInterface::class);
    }
}
