<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Ticket;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CommentaireController extends AbstractController
{
    #[Route('/ticket/{id}/comment', name: 'app_commentaire_add')]
    public function add(Request $request, Ticket $ticket, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setTicket($ticket);
            $commentaire->setAuteur($this->getUser());

            // Gestion du fichier uploadé
            $pieceJointe = $form->get('pieceJointe')->getData();
            if ($pieceJointe) {
                $originalFilename = pathinfo($pieceJointe->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pieceJointe->guessExtension();

                try {
                    $pieceJointe->move(
                        $this->getParameter('commentaires_directory'),
                        $newFilename
                    );
                    $commentaire->setPieceJointe($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload du fichier');
                }
            }

            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès');
            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }

        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/commentaire/{id}/delete', name: 'app_commentaire_delete')]
    public function delete(Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        // Vérification que l'utilisateur est l'auteur du commentaire
        if ($this->getUser() !== $commentaire->getAuteur()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce commentaire');
        }

        $ticketId = $commentaire->getTicket()->getId();
        
        // Suppression de la pièce jointe si elle existe
        if ($commentaire->getPieceJointe()) {
            $filePath = $this->getParameter('commentaires_directory').'/'.$commentaire->getPieceJointe();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $entityManager->remove($commentaire);
        $entityManager->flush();

        $this->addFlash('success', 'Commentaire supprimé avec succès');
        return $this->redirectToRoute('app_ticket_show', ['id' => $ticketId]);
    }
}
