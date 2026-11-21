<?php

namespace App\Controller;

use App\DTO\GroupSearchDTO;
use App\Form\GroupSearchType;
use App\Service\GroupementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GroupementController extends AbstractController
{
    public function __construct(
        private readonly GroupementService $groupementService
    ) {}

    #[Route('/', name: 'app_home')]
    #[Route('/groupements', name: 'app_groupement_index')]
    public function index(Request $request): Response
    {
        $searchDTO = new GroupSearchDTO();
        $form = $this->createForm(GroupSearchType::class, $searchDTO);
        $form->handleRequest($request);

        // Récupération sécurisée du numéro de page
        $page = max(1, $request->query->getInt('page', 1));

        // Récupération paginée avec filtre
        $pageDTO = $this->groupementService->getPaginatedGroupements($searchDTO, $page, limit: 6);

        // Métriques globales pour la bannière Calebasse Centrale
        $stats = $this->groupementService->getGlobalStats();

        return $this->render('groupement/index.html.twig', [
            'form' => $form->createView(),
            'pageDTO' => $pageDTO,
            'groupements' => $pageDTO->elements,
            'stats' => $stats,
            'search' => $searchDTO,
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
