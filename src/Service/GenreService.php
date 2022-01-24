<?php

namespace App\Service;

use App\Entity\Genre;

class GenreService extends AbstractService
{
    /**
     * @param string $nom
     *
     * @return Genre
     */
    public function addGenre(string $nom): ?Genre
    {
        $genre = new Genre();
        $genre->setNom($nom);
        if (\count($this->validator->validate($genre)) === 0) {
            $this->entityManager->persist($genre);
            $this->entityManager->flush();
        } else {
            $genre = null;
        }

        return $genre;
    }

    /**
     * @return array<Genre>
     */
    public function getAllGenres(): array
    {
        return $this->entityManager->getRepository(Genre::class)->findAll();
    }

    /**
     * @param int $genreId
     *
     * @return Genre|null
     */
    public function getGenre(int $genreId): ?Genre
    {
        return $this->entityManager->getRepository(Genre::class)->find($genreId);
    }

    /**
     * @param Genre $genre
     * @param string $nom
     *
     * @return Genre|null
     */
    public function updateGenre(Genre $genre, string $nom): ?Genre
    {
        $genre->setNom($nom);
        if (\count($this->validator->validate($genre)) === 0) {
            $this->entityManager->flush();
        } else {
            $genre = null;
        }

        return $genre;
    }

    public function hasBooks(Genre $genre): bool
    {
        return $genre->getLivres()->count() > 0;
    }

    /**
     * @param Genre $genre
     *
     * @return bool
     */
    public function deleteGenre(Genre $genre): bool
    {
        try {
            $this->entityManager->remove($genre);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
}
