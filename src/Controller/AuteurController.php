<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Service\AuteurService;
use App\Service\GenreService;
use App\Service\LivreService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuteurController extends AbstractController
{
    /**
     * @Route("/auteurs", name="list_auteurs")
    */
    public function list(AuteurService $auteurService): Response
    {
        $auteurs = $auteurService->getAllAuteurs();

        return $this->render('auteur/index.html.twig', [
            'auteurs' => $auteurs,
        ]);
    }

    /**
     * @Route("/auteurs/add", name="add_auteur")
     */
    public function add(Request $request, AuteurService $auteurService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($request->request->has('submit_form')) {
            $data = $this->getRequestParams($request);
            $auteur = $auteurService->addAuteur(
                $data['nom'],
                $data['sexe'],
                $data['dateNaissance'],
                $data['nationalite']
            );
            if (null === $auteur) {
                return $this->redirectToRoute(
                    'generic_error',
                    ['errorMessage' => 'Auteur invalide.']
                );
            }

            return $this->redirectToRoute('list_auteurs');
        }

        return $this->render('auteur/detail.html.twig', [
            'auteur' => new Auteur(),
        ]);
    }

    /**
     * @Route("/auteurs/detail/{auteur}", name="auteur_detail", requirements={"auteur"="[1-9]\d*"})
     */
    public function detail(
        Request $request,
        AuteurService $auteurService,
        int $auteur
    ): Response
    {
        $auteur = $auteurService->getAuteur($auteur);
        if (!$auteur) {
            throw $this->createNotFoundException('Auteur introuvable');
        }
        if ($request->request->has('submit_form')) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $data = $this->getRequestParams($request);
            $auteur = $auteurService->updateAuteur(
                $auteur,
                $data['nom'],
                $data['sexe'],
                $data['dateNaissance'],
                $data['nationalite']
            );
            if (null === $auteur) {
                return $this->redirectToRoute(
                    'generic_error',
                    ['errorMessage' => 'Auteur invalide.']
                );
            }
        }

        return $this->render('auteur/detail.html.twig', [
            'auteur' => $auteur,
        ]);
    }

    /**
     * @Route("/auteurs/delete/{auteur}", name="auteur_delete", requirements={"auteur"="[1-9]\d*"})
     */
    public function delete(
        AuteurService $auteurService,
        LivreService $livreService,
        int $auteur
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $auteur = $auteurService->getAuteur($auteur);
        if (!$auteur) {
            throw $this->createNotFoundException('Auteur introuvable');
        }
        foreach ($auteur->getLivres() as $livre) {
            if ($livre->getAuteurs()->count() === 1) {
                $livreService->deleteLivre($livre);
            }
        }

        if (!$auteurService->deleteAuteur($auteur)) {
            return $this->redirectToRoute(
                'generic_error',
                ['errorMessage' => 'Erreur, veuillez re-éssayer.']
            );
        }

        return $this->redirectToRoute('list_auteurs');
    }

    private function getRequestParams(Request $request): array
    {
        $dateNaissance = \strip_tags($request->request->get('form_date_naissance'));
        $dateNaissance = \DateTime::createFromFormat('Y-m-d', $dateNaissance);
        $dateNaissance = $dateNaissance ?: new \DateTime();;
        return [
            'nom' => \strip_tags($request->request->get('form_nom')), // XSS protection
            'sexe' => \strip_tags($request->request->get('form_sexe')),
            'dateNaissance' => $dateNaissance,
            'nationalite' => \strip_tags($request->request->get('form_nationalite')),
        ];
    }
}