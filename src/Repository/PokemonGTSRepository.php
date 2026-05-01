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
        public function searchPokemon($species, $minlevel, $maxlevel, $gender, $pid): array
        {
            $qb = $this->createQueryBuilder('p')
                ->andWhere('p.dex_id = :species')
                ->andWhere('CAST(p.level AS UNSIGNED) >= :minlevel')
                ->andWhere('CAST(p.level AS UNSIGNED) <= :maxlevel')
                ->andWhere('p.pid != :pid')
                ->andWhere('p.is_exchanged = 0')
                ->setParameter('species', $species)
                ->setParameter('minlevel', $minlevel)
                ->setParameter('maxlevel', $maxlevel)
                ->setParameter('pid', $pid)
            ;

            if($gender=="ANY"){
                $query = $qb->orderBy('p.id', 'ASC')->setMaxResults(7)->getQuery();
                return $query->execute();
            }

            $qb->andWhere('p.gender = :gender')->setParameter('gender', $gender);
            $query = $qb->orderBy('p.id', 'ASC')->setMaxResults(7)->getQuery();
            return $query->execute();
        }

        // Get the player's deposited pokemon or the pokemon that's been traded to them
        public function findDepositedPokemon($pid): ?PokemonGTS
        {
            return $this->createQueryBuilder('p')
                ->where('p.pid = :val')
                ->andWhere('p.is_exchanged < 3')
                ->setParameter('val', $pid)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }

        // Get the pokemon the player is trying to trade with
        public function findExchangedPokemon($pid): ?PokemonGTS
        {
            return $this->createQueryBuilder('p')
                ->where('p.pid = :val')
                ->andWhere('p.is_exchanged = 2')
                ->setParameter('val', $pid)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }


        public function find20Pokemon(): array
        {

            $qb = $this->createQueryBuilder('p')
                ->orderBy('p.id', 'ASC')
                ->setMaxResults(20)
            ;

            $query = $qb->getQuery();
            return $query->execute();
        }
}
