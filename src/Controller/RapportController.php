<?php

namespace App\Controller;

use App\Entity\Rapport;
use App\Form\RapportInterventionType;
use App\Form\RapportStatistiqueType;
use App\Repository\RapportRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/rapport')]
class RapportController extends AbstractController
{
    #[Route('/', name: 'app_rapport_index', methods: ['GET'])]
    public function index(RapportRepository $rapportRepository): Response
    {
        return $this->render('rapport/index.html.twig', [
            'rapports' => $rapportRepository->findLatest(),
        ]);
    }

    #[Route('/intervention/new', name: 'app_rapport_intervention_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TECHNICIEN')]
    public function newIntervention(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rapport = new Rapport();
        $rapport->setType(Rapport::TYPE_INTERVENTION)
                ->setAuteur($this->getUser())
                ->setDateCreation(new \DateTime());

        $form = $this->createForm(RapportInterventionType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rapport);
            $entityManager->flush();

            $this->addFlash('success', 'Rapport d\'intervention créé avec succès');
            return $this->redirectToRoute('app_rapport_show', ['id' => $rapport->getId()]);
        }

        return $this->render('rapport/new_intervention.html.twig', [
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    #[Route('/statistique/new', name: 'app_rapport_statistique_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newStatistique(
        Request $request, 
        EntityManagerInterface $entityManager,
        TicketRepository $ticketRepository
    ): Response
    {
        $rapport = new Rapport();
        $rapport->setType(Rapport::TYPE_STATISTIQUES)
                ->setAuteur($this->getUser())
                ->setDateCreation(new \DateTime());

        $form = $this->createForm(RapportStatistiqueType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dateDebut = $form->get('dateDebut')->getData();
            $dateFin = $form->get('dateFin')->getData();

            // Récupérer tous les tickets de la période
            $tickets = $ticketRepository->findByDateRange($dateDebut, $dateFin);
            foreach ($tickets as $ticket) {
                $rapport->addTicket($ticket);
            }

            // Générer les statistiques
            $rapport->genererRapportStatistique($dateDebut, $dateFin);

            $entityManager->persist($rapport);
            $entityManager->flush();

            $this->addFlash('success', 'Rapport statistique généré avec succès');
            return $this->redirectToRoute('app_rapport_show', ['id' => $rapport->getId()]);
        }

        return $this->render('rapport/new_statistique.html.twig', [
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rapport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est soit l'auteur, soit un admin
        if (!$this->isGranted('ROLE_ADMIN') && $rapport->getAuteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de modifier ce rapport.');
        }

        // Choisir le bon type de formulaire selon le type de rapport
        $formType = $rapport->getType() === Rapport::TYPE_INTERVENTION 
            ? RapportInterventionType::class 
            : RapportStatistiqueType::class;

        $form = $this->createForm($formType, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si c'est un rapport statistique, mettre à jour les statistiques
            if ($rapport->getType() === Rapport::TYPE_STATISTIQUES && $form->has('dateDebut') && $form->has('dateFin')) {
                $dateDebut = $form->get('dateDebut')->getData();
                $dateFin = $form->get('dateFin')->getData();
                $rapport->genererRapportStatistique($dateDebut, $dateFin);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Rapport modifié avec succès');
            return $this->redirectToRoute('app_rapport_show', ['id' => $rapport->getId()]);
        }

        return $this->render('rapport/edit.html.twig', [
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rapport_show', methods: ['GET'])]
    public function show(Rapport $rapport): Response
    {
        return $this->render('rapport/show.html.twig', [
            'rapport' => $rapport,
        ]);
    }

    #[Route('/service/{service}', name: 'app_rapport_by_service', methods: ['GET'])]
    public function byService(string $service, RapportRepository $rapportRepository): Response
    {
        $rapports = $rapportRepository->findByService($service);

        return $this->render('rapport/by_service.html.twig', [
            'rapports' => $rapports,
            'service' => $service,
        ]);
    }

    #[Route('/ticket/{id}/interventions', name: 'app_rapport_ticket_interventions', methods: ['GET'])]
    public function ticketInterventions(int $id, RapportRepository $rapportRepository): Response
    {
        $rapports = $rapportRepository->findInterventionsForTicket($id);

        return $this->render('rapport/ticket_interventions.html.twig', [
            'rapports' => $rapports,
        ]);
    }

    #[Route('/{id}', name: 'app_rapport_delete', methods: ['POST'])]
    public function delete(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est soit l'auteur, soit un admin
        if (!$this->isGranted('ROLE_ADMIN') && $rapport->getAuteur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer ce rapport.');
        }

        if ($this->isCsrfTokenValid('delete'.$rapport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rapport);
            $entityManager->flush();
            $this->addFlash('success', 'Rapport supprimé avec succès');
        }

        return $this->redirectToRoute('app_rapport_index');
    }
}
