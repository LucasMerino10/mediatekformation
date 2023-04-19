<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    
    private const P_ID_ID = "p.id id";
    private const P_NAME_NAME = "p.name name";
    private const C_NAME = "c.name";
    private const P_FORMATIONS = "p.formations";      
    private const C_NAME_CATEGORIENAME = "c.name categoriename";
    private const F_CATEGORIES = "f.categories";
    private const P_ID = "p.id";
    private const P_NAME = "p.name";
                
    /**
     * Constructeur de la classe
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    /**
     * Permet l'ajout d'une playlist
     * @param Playlist $entity
     * @param bool $flush
     * @return void
     */
    public function add(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Permet la suppression d'une playlist
     * @param Playlist $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * Retourne toutes les playlists triées par nom
     * @param type $champ
     * @param type $ordre
     * @return Playlist[]
     */
    public function findAllOrderByName($ordre): array{
        return $this->createQueryBuilder('p')           
                ->leftjoin(self::P_FORMATIONS, 'f')
                ->groupBy(self::P_ID)
                ->orderBy(self::P_NAME, $ordre)
                ->getQuery()
                ->getResult();       
    }
    
    /**
     * Retourne toutes les playlists triées par nombre de formations
     * @param type $ordre
     * @return array
     */
    public function findAllOrderByNbFormations($ordre): array{
        return $this->createQueryBuilder('p')
                ->leftJoin(self::P_FORMATIONS, 'f')
                ->groupBy(self::P_ID)
                ->orderBy('count(f.title)', $ordre)
                ->getQuery()
                ->getResult();
    }

    /**
     * Retourne les playlist dont le nom contient la valeur du champ renseigné
     * ou retourne tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @return Playlist[]
     */
    public function findByContainValue($champ, $valeur): array{
        if($valeur==""){
            return $this->findAllOrderByName('ASC');
        }    
        else{    
            return $this->createQueryBuilder('p')
                    ->leftjoin(self::P_FORMATIONS, 'f')
                    ->leftjoin(self::F_CATEGORIES, 'c')
                    ->where('p.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy(self::P_ID)
                    ->orderBy(self::P_NAME, 'ASC')
                    ->getQuery()
                    ->getResult();                                   
        }           
    }   
    
    /**
     * Retourne les playlist dont la catégorie correspond avec la valeur du 
     * champ ou retourne tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @param type $table si $champ dans une autre table
     * @return Playlist[]
     */
    public function findByContainValueTable($champ, $valeur, $table): array{
        if($valeur==""){
           return $this->findAllOrderByName('ASC');
        }  
        else{
            return $this->createQueryBuilder('p')
                    ->leftjoin(self::P_FORMATIONS, 'f')
                    ->leftjoin(self::F_CATEGORIES, 'c')
                    ->where('c.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy(self::P_ID)
                    ->orderBy(self::P_NAME, 'ASC')
                    ->getQuery()
                    ->getResult(); 
        }
    }   
}
