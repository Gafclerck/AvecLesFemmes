<?php

namespace App\Controller;

use App\Service\CampagneService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CampagneController extends AbstractController
{
    public function __construct(
        private readonly CampagneService $campagneService
    ) {}

    #[Route('/campagnes/{id}', name: 'app_campagne_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $data = $this->campagneService->getCampagneDetail($id);

        if (!$data) {
            throw $this->createNotFoundException(sprintf('La campagne n°%d n\'existe pas.', $id));
        }

        return $this->render('campagne/show.html.twig', [
            'campagne' => $data['campagne'],
            'participants' => $data['participants'],
            'seances' => $data['seances'],
        ]);
    }

    #[Route('/campagnes/{id}/seances/{date}', name: 'app_campagne_seance_show', requirements: ['id' => '\d+', 'date' => '\d{4}-\d{2}-\d{2}'])]
    public function seance(int $id, string $date): Response
    {
        $data = $this->campagneService->getSeanceDetail($id, $date);

        if (!$data) {
            throw $this->createNotFoundException(sprintf('La séance du %s est introuvable.', $date));
        }

        return $this->render('campagne/seance.html.twig', [
            'campagne' => $data['campagne'],
            'seance' => $data['seance'],
        ]);
    }
}
