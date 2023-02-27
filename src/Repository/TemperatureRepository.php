<?php

namespace App\Repository;

use App\Entity\Sensor;
use App\Entity\Temperature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Temperature>
 *
 * @method Temperature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Temperature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Temperature[]    findAll()
 * @method Temperature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemperatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Temperature::class);
    }

    public function save(Temperature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Temperature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

	/**
	 * @throws NonUniqueResultException
	 * @throws \Exception
	 */
	public function getMiddleTemperature($start = false, $end = false)
	{
		$query = $this->createQueryBuilder('t')
			->select('AVG(t.temperature) as temperature')
			->where('(t.timestamp >= :start AND t.timestamp <= :end)')
			->setParameter('start', new \DateTime($start . " 00:00:00"))
			->setParameter('end',  new \DateTime($end . " 23:59:59"));

		return $query->getQuery()->getOneOrNullResult();
	}

	/**
	 * @throws \Exception
	 */
	public function getMiddleTemperatureForSensor(Sensor $sensor, $date)
	{
		$query = $this->createQueryBuilder('t')
			->select("date_trunc('hour', t.timestamp) as timestamp, AVG(t.temperature) as temperature")
			->innerJoin('t.sensor', 's')
			->where('(t.timestamp >= :start AND t.timestamp <= :end)')
			->andWhere('s.id = :uuid')
			->setParameter('start', new \DateTime($date . " 00:00:00"))
			->setParameter('end',  new \DateTime($date . " 23:59:59"))
			->setParameter('uuid', $sensor->getId())
			->groupBy('timestamp');

		return $query->getQuery()->getResult();
	}

}
