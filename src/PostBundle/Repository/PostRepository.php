<?php

namespace PostBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use PostBundle\Entity\Post;

/**
 * Class PostRepository
 * @package PostBundle\Repository
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function addPost($review, $authorId, $book)
    {
        $entityManager = $this->getEntityManager();
        $post = new Post($authorId, $review, 0, new \DateTime(), $book);

        $book->setReviews(new ArrayCollection([$post]));
        try {
            $entityManager->persist($book);
            $entityManager->persist($post);
            $entityManager->flush();
            return true;
        } catch (ORMException $e) {
            return false;
        }
    }

    /**
     * Returns all the reviews for a book when bookId is passed.
     *
     * @param $bookId
     *
     * @return array
     */
    public function getBookReviews($bookId)
    {
        $queryBuilder = $this->createQueryBuilder('post')
            ->andWhere('post.book = :bookId')
            ->setParameter('bookId', $bookId);

        $query = $queryBuilder->getQuery();
        $reviews = $query->getResult();

        return $reviews;
    }

    public function updatePost($newText, Post $post)
    {
        $entityManager = $this->getEntityManager();
        $post->setReviewText($newText);
        try {
            $entityManager->persist($post);
            $entityManager->flush();
            return true;
        } catch (ORMException $e) {
            return false;
        }
    }

    public function getPostsHelpful($postId)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT posts.helpful FROM posts WHERE id = :id");
        $statement->bindValue('id', $postId);
        $statement->execute();
        $results = $statement->fetchAll();

        return $results;
    }

    /**
     * Register voting helpful
     *
     * @param $postId
     * @param $userId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function registerVote($postId, $userId)
    {
        $connection = $this->getEntityManager()->getConnection();

        $isVoteExist = $this->checkIfUserVoted($postId, $userId);

//        var_dump($isVoteExist);

//        die();
        $sql = "INSERT INTO register_vote (post_id, user_id) VALUES ($postId, $userId)";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
    }

    /**
     * Checks if a user has already voted for a post.
     *
     * @param $postId
     * @param $userId
     *
     * @return bool
     */
    public function checkIfUserVoted($postId, $userId)
    {
        $rsm = new ResultSetMapping();
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createNativeQuery('SELECT * FROM register_vote WHERE register_vote.post_id = ? AND register_vote.user_id = ?', $rsm)
                                ->setParameter(1, $postId)
                                ->setParameter(2, $userId);
        $results = $query->getResult();
        var_dump($results);
        if (!empty($results)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $userId
     * @param $bookId
     *
     * @return bool
     */
    public function checkIfUserWrotePost($userId, $bookId)
    {
        $queryBuilder = $this->createQueryBuilder('post')
                            ->andWhere('post.book = :bookId')
                            ->andWhere('post.author = :userId')
                            ->setParameter('bookId', $bookId)
                            ->setParameter('userId', $userId);

        $query = $queryBuilder->getQuery();
        $reviews = $query->getResult();

        if (empty($reviews)){
            return false;
        }else {
            return true;
        }
    }

    /**
     * Update helpful when user clicks on the '+' button.
     *
     * @param $postId
     *
     * @return bool
     */
    public function helpful($postId)
    {

        $post = $this->find($postId);
        $entityManager = $this->getEntityManager();
        if (isset($post)){
            $post->setHelpful($post->getHelpful() + 1);

            try {
                $entityManager->persist($post);
                $entityManager->flush();
                return true;
            } catch (ORMException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function deletePostById($id)
    {
        $post = $this->find($id);
        if(isset($post)){
            $entityManager = $this->getEntityManager();
            try {
                $entityManager->remove($post);
            } catch (ORMException $e) {
            }
            try {
                $entityManager->flush();
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
            }

            return true;
        }
        return false;
    }
}
