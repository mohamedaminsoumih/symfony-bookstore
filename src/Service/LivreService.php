<?php

namespace App\Service;

use App\Entity\Auteur;
use App\Entity\Genre;
use App\Entity\Livre;
use Doctrine\Common\Collections\Criteria;

class LivreService extends AbstractService
{
    /**
     * @param string $titre
     * @param int $note
     * @param int $pages
     * @param \DateTimeInterface $dateParution
     * @param string $isbn
     * @param array $auteurs
     * @param array $genres
     *
     * @return Livre
     */
    public function addLivre(
        string $titre,
        int $note,
        int $pages,
        \DateTimeInterface $dateParution,
        string $isbn,
        array $auteurs,
        array $genres
    ): ?Livre
    {
        $livre = new Livre();
        $livre->setTitre($titre);
        $livre->setNote($note);
        $livre->setNombrePages($pages);
        $livre->setIsbn($isbn);
        $livre->setDateDeParution($dateParution);
        $auteurRepo = $this->entityManager->getRepository(Auteur::class);
        $genreRepo = $this->entityManager->getRepository(Genre::class);
        foreach ($auteurs as $auteur) {
            if (
                ($auteur = $auteurRepo->find((int) $auteur)) !== null &&
                !$livre->getAuteurs()->contains($auteur)
            ) {
                $livre->addAuteur($auteur);
            }
        }
        foreach ($genres as $genre) {
            if (
                ($genre = $genreRepo->find((int) $genre)) !== null &&
                !$livre->getGenres()->contains($genre)
            ) {
                $livre->addGenre($genre);
            }
        }
        if (\count($this->validator->validate($livre)) === 0) {
            $this->entityManager->persist($livre);
            $this->entityManager->flush();
        } else {
            $livre = null;
        }

        return $livre;
    }

    /**
     * @param Livre $livre
     * @param string $titre
     * @param int $note
     * @param int $pages
     * @param \DateTimeInterface $dateParution
     * @param string $isbn
     * @param array $auteurs
     * @param array $genres
     *
     * @return Auteur|null
     */
    public function updateLivre(
        Livre $livre,
        string $titre,
        int $note,
        int $pages,
        \DateTimeInterface $dateParution,
        array $auteurs,
        array $genres
    ): ?Livre
    {
        $livre->setTitre($titre);
        $livre->setNote($note);
        $livre->setNombrePages($pages);
        $livre->setDateDeParution($dateParution);
        $auteurRepo = $this->entityManager->getRepository(Auteur::class);
        $genreRepo = $this->entityManager->getRepository(Genre::class);
        foreach ($auteurs as $auteur) {
            if (
                ($auteur = $auteurRepo->find((int) $auteur)) !== null &&
                !$livre->getAuteurs()->contains($auteur)
            ) {
                $livre->addAuteur($auteur);
            }
        }
        foreach ($genres as $genre) {
            if (
                ($genre = $genreRepo->find((int) $genre)) !== null &&
                !$livre->getGenres()->contains($genre)
            ) {
                $livre->addGenre($genre);
            }
        }
        if (\count($this->validator->validate($livre)) === 0) {
            $this->entityManager->persist($livre);
            $this->entityManager->flush();
        } else {
            $livre = null;
        }

        return $livre;
    }

    /**
     * @return array<Livre>
     */
    public function getAllBooks(
        array $criteria
    ): array
    {
        return $this->entityManager->getRepository(Livre::class)->findByCriteria([
            'titre' => $criteria['titre'],
            'from' => $criteria['from'],
            'to' => $criteria['to'],
        ]);
    }

    public function getLivre(int $livreId): ?Livre
    {
        return $this->entityManager->getRepository(Livre::class)->find($livreId);
    }

    /**
     * @param Livre $livre
     *
     * @return bool
     */
    public function deleteLivre(Livre $livre): bool
    {
        try {
            $this->entityManager->remove($livre);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
}
