<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Service\GenreService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    /**
     * @Route("/genres", name="list_genres")
     */
    public function list(GenreService $genreService): Response
    {
        $genres = $genreService->getAllGenres();

        return $this->render('genre/index.html.twig', [
            'genres' => $genres,
        ]);
    }

    /**
     * @Route("/genres/add", name="add_genre")
     */
    public function add(Request $request, GenreService $genreService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($request->request->has('submit_form')) {
            $nom = \strip_tags($request->request->get('form_nom')); // XSS protection
            if (null === $genreService->addGenre($nom)) {
                return $this->redirectToRoute(
                    'generic_error',
                    ['errorMessage' => 'Genre invalide.']
                );
            }

            return $this->redirectToRoute('list_genres');
        }

        return $this->render('genre/detail.html.twig', [
            'genre' => new Genre(),
        ]);
    }

    /**
     * @Route("/genres/detail/{genre}", name="genre_detail", requirements={"genre"="[1-9]\d*"})
     */
    public function detail(
        Request      $request,
        GenreService $genreService,
        int          $genre
    ): Response
    {
        $genre = $genreService->getGenre($genre);
        if (!$genre) {
            throw $this->createNotFoundException('Genre introuvable');
        }
        if ($request->request->has('submit_form')) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $nom = \strip_tags($request->request->get('form_nom')); // XSS protection
            $genre = $genreService->updateGenre($genre, $nom);
            if (null === $genre) {
                return $this->redirectToRoute(
                    'generic_error',
                    ['errorMessage' => 'Genre invalide.']
                );
            }
        }

        return $this->render('genre/detail.html.twig', [
            'genre' => $genre,
        ]);
    }

    /**
     * @Route("/genres/delete/{genre}", name="genre_delete", requirements={"genre"="[1-9]\d*"})
     */
    public function delete(
        GenreService $genreService,
        int          $genre
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $genre = $genreService->getGenre($genre);
        if (!$genre) {
            throw $this->createNotFoundException('Genre introuvable');
        }
        if ($genreService->hasBooks($genre)) {
            return $this->redirectToRoute(
                'generic_error',
                ['errorMessage' => 'Ce genre ne peut être supprimé. Il comporte des livres.']
            );
        }
        if (!$genreService->deleteGenre($genre)) {
            return $this->redirectToRoute(
                'generic_error',
                ['errorMessage' => 'Erreur, veuillez re-éssayer.']
            );
        }

        return $this->redirectToRoute('list_genres');
    }
}