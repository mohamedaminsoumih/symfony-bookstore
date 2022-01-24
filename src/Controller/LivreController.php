<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Service\AuteurService;
use App\Service\GenreService;
use App\Service\LivreService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivreController extends AbstractController
{
    /**
     * @Route("/", name="index_page")
     * @Route("/livres", name="list_livres")
    */
    public function list(
        Request $request,
        LivreService $livreService
    ): Response
    {
        $criteria = ['titre' => null, 'from' => null, 'to' => null];
        if ($request->request->has('search-livre')) {
            $dateFrom = \DateTime::createFromFormat('Y-m-d', $request->request->get('date-from'));
            $dateFrom = $dateFrom ?: null;
            $dateTo = \DateTime::createFromFormat('Y-m-d', $request->request->get('date-to'));
            $dateTo = $dateTo ?: null;
            $criteria = [
                'titre' => $request->request->get('titre'),
                'from' => $dateFrom,
                'to' => $dateTo,
            ];
        }
        $livres = $livreService->getAllBooks($criteria);

        return $this->render('livre/index.html.twig', [
            'livres' => $livres,
            'dateFrom' => $criteria['from'],
            'dateTo' => $criteria['to'],
            'titre' => $criteria['titre'],
        ]);
    }

    /**
     * @Route("/livres/add", name="add_livre")
     */
    public function add(
        Request $request,
        LivreService $livreService,
        AuteurService $auteurService,
        GenreService $genreService
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($request->request->has('submit_form')) {
            $data = $this->getRequestParams($request);
            $auteur = $livreService->addLivre(
                $data['titre'],
                $data['note'],
                $data['pages'],
                $data['dateParution'],
                $data['isbn'],
                $data['auteurs'],
                $data['genres']
            );
            if (null === $auteur) {
                return $this->redirectToRoute(
                    'generic_error',
                    ['errorMessage' => 'Livre invalide.']
                );
            }

            return $this->redirectToRoute('list_livres');
        }

        return $this->render('livre/detail.html.twig', [
            'livre' => new Livre(),
            'auteurs' => $auteurService->getAllAuteurs(),
            'genres' => $genreService->getAllGenres(),
        ]);
    }

    /**
     * @Route("/livres/detail/{livre}", name="livre_detail", requirements={"livre"="[1-9]\d*"})
     */
    public function detail(
        Request $request,
        LivreService $livreService,
        AuteurService $auteurService,
        GenreService $genreService,
        int $livre
    ): Response
    {
        $livre = $livreService->getLivre($livre);
        if (!$livre) {
            throw $this->createNotFoundException('Livre introuvable');
        }
        if ($request->request->has('submit_form')) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $data = $this->getRequestParams($request);
            $auteur = $livreService->updateLivre(
                $livre,
                $data['titre'],
                $data['note'],
                $data['pages'],
                $data['dateParution'],
                $data['auteurs'],
                $data['genres']
            );
            if (null === $auteur) {
                return $this->redirectToRoute(
                    'generic_error',
                    ['errorMessage' => 'Livre invalide.']
                );
            }
        }

        return $this->render('livre/detail.html.twig', [
            'livre' => $livre,
            'auteurs' => $auteurService->getAllAuteurs(),
            'genres' => $genreService->getAllGenres(),
        ]);
    }

    /**
     * @Route("/livres/delete/{livre}", name="livre_delete", requirements={"livre"="[1-9]\d*"})
     */
    public function delete(
        LivreService $livreService,
        int $livre
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $livre = $livreService->getLivre($livre);
        if (!$livre) {
            throw $this->createNotFoundException('Auteur introuvable');
        }

        if (!$livreService->deleteLivre($livre)) {
            return $this->redirectToRoute(
                'generic_error',
                ['errorMessage' => 'Erreur, veuillez re-éssayer.']
            );
        }

        return $this->redirectToRoute('list_livres');
    }

    private function getRequestParams(Request $request): array
    {
        $dateParution = \strip_tags($request->request->get('form_date_parution'));
        $dateParution = \DateTime::createFromFormat('Y-m-d', $dateParution);
        $dateParution = $dateParution ?: new \DateTime();;
        $auteurs = $request->request->get('form_auteurs');
        $auteurs = \is_array($auteurs) ? $auteurs : [];
        $auteurs = \array_filter($auteurs, function ($v, $i) {
            return \is_numeric($v);
        }, ARRAY_FILTER_USE_BOTH);
        $genres = $request->request->get('form_genres');
        $genres = \is_array($genres) ? $genres : [];
        $genres = \array_filter($genres, function ($v, $i) {
            return \is_numeric($v);
        }, ARRAY_FILTER_USE_BOTH);
        $pages = \strip_tags($request->request->get('form_pages'));
        $pages = \is_numeric($pages) ? (int) $pages : 0;
        $note = \strip_tags($request->request->get('form_note'));
        $note = \is_numeric($note) ? (int) $note : 0;
        return [
            'titre' => \strip_tags($request->request->get('form_titre')), // XSS protection
            'pages' => $pages,
            'dateParution' => $dateParution,
            'isbn' => \str_replace('-', '', \strip_tags($request->request->get('form_isbn'))),
            'note' => $note,
            'auteurs' => $auteurs,
            'genres' => $genres,
        ];
    }
}