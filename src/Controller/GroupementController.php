<?php

namespace App\Controller;

use App\Service\GroupementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GroupementController extends AbstractController
{
    public function __construct(
        private readonly GroupementService $groupementService
    ) {}

    #[Route('/', name: 'app_home')]
    #[Route('/groupements', name: 'app_groupement_index')]
    public function index(): Response
    {
        $groupements = $this->groupementService->getGroupementsForListing();

        // Calcul des agrégats pour la Calebasse Centrale
        $totalCaisse = 0.0;
        $totalMembres = 0;
        $totalCampagnes = 0;

        foreach ($groupements as $g) {
            $totalCaisse += (float) str_replace([' ', 'FCFA'], '', $g->soldeCaisseSociale);
            $totalMembres += $g->nombreMembres;
            $totalCampagnes += $g->nombreCampagnes;
        }

        return $this->render('groupement/index.html.twig', [
            'groupements' => $groupements,
            'total_caisse' => number_format($totalCaisse, 0, ',', ' ') . ' FCFA',
            'total_membres' => $totalMembres,
            'total_campagnes' => $totalCampagnes,
            'total_groupements' => count($groupements),
        ]);
    }

    #[Route('/groupements/{id}', name: 'app_groupement_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $groupement = $this->groupementService->getGroupementDetail($id);

        if (!$groupement) {
            throw $this->createNotFoundException(sprintf('Le groupement n°%d n\'existe pas.', $id));
        }

        return $this->render('groupement/show.html.twig', [
            'groupement' => $groupement,
        ]);
    }
}
