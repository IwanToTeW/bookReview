<?php

namespace BookBundle\Repository;

use BookBundle\Entity\Book;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Class BookRepository
 * @package BookBundle\Repository
 */
class BookRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllBooks($offset, $limit)
    {
        $queryBuilder = $this->createQueryBuilder('book')
                                ->orderBy('book.title', 'ASC');
//                                ->setFirstResult($offset)
//                                ->setMaxResults($limit);
        $query = $queryBuilder->getQuery();


        $books = $query->getResult();

        return $books;
    }

    public function getBookById($bookId)
    {
        $queryBuilder = $this->createQueryBuilder('book')
            ->orderBy('book.title', 'ASC')
            ->andWhere('book.id  = :bookId');
        $queryBuilder->setParameter('bookId', $bookId);

        $query = $queryBuilder->getQuery();


        $book = $query->getResult();

        return $book;
    }

    public function addBook(Book $book)
    {
        $result = $this->findOneBy(['title'=> $book->getTitle()]);
        if($result instanceof Book) {

            return false;
        } else {
            try {
                $this->getEntityManager()->persist($book);

            } catch (ORMException $e) {
            }
            try {
                $this->getEntityManager()->flush($book);
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }
            return true;
        }
    }

    public function removeBook($id)
    {
        $query = $this->getEntityManager()->createQuery(
            'DELETE FROM stb917_awd.books WHERE id = :id'
        )->setParameter('id', $id);
    }

    /**
     * @param $searchTerm
     * @param $searchBy
     *
     * @return array
     */
    public function searchBooks($searchTerm, $searchBy)
    {
        $queryBuilder = $this->createQueryBuilder('book');
        // Determine searching by what criteria it is going to be performed.
        if($searchBy === 'author'){
            $queryBuilder->andWhere('book.author LIKE :searchTerm');
        }elseif ($searchBy === 'category') {
            $queryBuilder->andWhere('book.category LIKE :searchTerm');
        } else{
            $queryBuilder->andWhere('book.title LIKE  :searchTerm');
        }

        $queryBuilder->setParameter('searchTerm', '%'. $searchTerm . '%')
            ->orderBy('book.title', ' ASC');

        $query = $queryBuilder->getQuery();
        $books = $query->getResult();

        return $books;
    }
}
