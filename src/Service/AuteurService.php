<?php

namespace App\Service;

use App\Entity\Auteur;

class AuteurService extends AbstractService
{
    /**
     * @param string $nom
     * @param string $sexe
     * @param \DateTimeInterface $dateNaissance
     * @param string $nationalite
     *
     * @return Auteur
     */
    public function addAuteur(
        string $nom,
        string $sexe,
        \DateTimeInterface $dateNaissance,
        string $nationalite
    ): ?Auteur
    {
        $auteur = new Auteur();
        $auteur->setNomPrenom($nom);
        $auteur->setSexe($sexe);
        $auteur->setDateDeNaissance($dateNaissance);
        $auteur->setNationalite($nationalite);
        if (\count($this->validator->validate($auteur)) === 0) {
            $this->entityManager->persist($auteur);
            $this->entityManager->flush();
        } else {
            $auteur = null;
        }

        return $auteur;
    }

    /**
     * @return array<Auteur>
     */
    public function getAllAuteurs(): array
    {
        return $this->entityManager->getRepository(Auteur::class)->findAll();
    }

    public function getAuteur(int $auteurId): ?Auteur
    {
        return $this->entityManager->getRepository(Auteur::class)->find($auteurId);
    }

    /**
     * @param Auteur $auteur
     * @param string $nom
     * @param string $sexe
     * @param \DateTimeInterface $dateNaissance
     * @param string $nationalite
     *
     * @return Auteur|null
     */
    public function updateAuteur(
        Auteur $auteur,
        string $nom,
        string $sexe,
        \DateTimeInterface $dateNaissance,
        string $nationalite
    ): ?Auteur
    {
        $auteur->setNomPrenom($nom);
        $auteur->setSexe($sexe);
        $auteur->setDateDeNaissance($dateNaissance);
        $auteur->setNationalite($nationalite);
        if (\count($this->validator->validate($auteur)) === 0) {
            $this->entityManager->flush();
        } else {
            $auteur = null;
        }

        return $auteur;
    }

    /**
     * @param Auteur $auteur
     *
     * @return bool
     */
    public function deleteAuteur(Auteur $auteur): bool
    {
        try {
            $this->entityManager->remove($auteur);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
}
