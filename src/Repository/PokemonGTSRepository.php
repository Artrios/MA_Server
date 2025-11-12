<?php

namespace App\Repository;

use App\Entity\PokemonGTS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PokemonGTS>
 */
class PokemonGTSRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PokemonGTS::class);
    }

        /**
         * @return PokemonGTS[] Returns an array of PokemonGTS objects
         */
        public function searchPokemon($species, $minlevel, $maxlevel, $gender): array
        {
            $qb = $this->createQueryBuilder('p')
                ->andWhere('p.dex_id = :species')
                ->andWhere('p.level >= :minlevel')
                ->andWhere('p.level <= :maxlevel')
                ->setParameter('species', $species)
                ->setParameter('minlevel', $minlevel)
                ->setParameter('maxlevel', $maxlevel)
            ;

            if($gender=="ANY"){
                $query = $qb->orderBy('p.id', 'ASC')->setMaxResults(10)->getQuery();
                return $query->execute();
            }

            $qb->andWhere('p.gender = :gender')->setParameter('gender', $gender);
            $query = $qb->orderBy('p.id', 'ASC')->setMaxResults(10)->getQuery();
            return $query->execute();
        }

        // Get the player's deposited pokemon or the pokemon that's been traded to them
        public function findDepositedPokemon($pid): ?PokemonGTS
        {
            return $this->createQueryBuilder('p')
                ->where('p.pid = :val')
                ->setParameter('val', $pid)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
}
