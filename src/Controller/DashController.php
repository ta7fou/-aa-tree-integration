<?php

namespace App\Controller;

use App\Entity\Campagne;
use App\Entity\Dash;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $dashEntries = $entityManager->getRepository(Dash::class)->findAll();

        $campagneVisites = [];
        $campagneParticipations = [];

        foreach ($dashEntries as $entry) {
            $campagneId = $entry->getCampagneId();

            if (!isset($campagneVisites[$campagneId])) {
                $campagneVisites[$campagneId] = 0;
            }

            if (!isset($campagneParticipations[$campagneId])) {
                $campagneParticipations[$campagneId] = 0;
            }

            $campagneVisites[$campagneId] += $entry->getNbVisites();
            $campagneParticipations[$campagneId] += $entry->getNbParticipations();
        }
        

        arsort($campagneVisites);
        arsort($campagneParticipations);

        // Récupérer les noms des campagnes à partir de leurs IDs
        $campagnes = $entityManager->getRepository(Campagne::class)->findAll();
        $campagneNames = [];

        foreach ($campagnes as $campagne) {
            $campagneId = $campagne->getId();
            $campagneNames[$campagneId] = $campagne->getNomcampagne();
        }

        // Obtenir les 5 campagnes les plus visitées
        $mostVisitedCampagnes = array_slice($campagneVisites, 0, 5, true);

        // Obtenir les 5 campagnes avec le plus de participations
        $mostParticipatedCampagnes = array_slice($campagneParticipations, 0, 5, true);

        // Formater les résultats avec les noms des campagnes
        $formattedMostVisitedCampagnes = [];
        foreach ($mostVisitedCampagnes as $campagneId => $visites) {
            $formattedMostVisitedCampagnes[] = [
                'nom_campagne' => $campagneNames[$campagneId],
                'total_visites' => $visites,
            ];
        }

        $formattedMostParticipatedCampagnes = [];
        foreach ($mostParticipatedCampagnes as $campagneId => $participations) {
            $formattedMostParticipatedCampagnes[] = [
                'nom_campagne' => $campagneNames[$campagneId],
                'total_participations' => $participations,
            ];
        }
        // Récupérer tous les événements
$events = $entityManager->getRepository(Event::class)->findAll();

// Créer un tableau associatif pour stocker le nombre de participants par campagne
$participantsByCampagneId = [];
foreach ($events as $event) {
    $campagne = $event->getCampagne();
    
    // Vérifier si l'événement est associé à une campagne
    if ($campagne) {
        $campagneId = $campagne->getId();
        $participantsByCampagneId[$campagneId] = $event->getNbParticipant();
        $campagneNames[$campagneId] = $campagne->getNomcampagne();
        
    }
}
// Récupérer les données de la table Dash
$dashData = $entityManager->getRepository(Dash::class)->findAll();

// Tableau pour stocker les pourcentages de participation par campagne
$participationPercentages = [];

// Calculer le pourcentage de participation pour chaque campagne
foreach ($dashData as $data) {
    $campagneId = $data->getCampagneId();
    $nbParticipations = $data->getNbParticipations();

    // Vérifier si l'événement correspondant existe dans $participantsByCampagneId
    if (isset($participantsByCampagneId[$campagneId])) {
        $nbParticipants = $participantsByCampagneId[$campagneId];
        if ($nbParticipants > 0) {
            $pourcentageParticipants = ($nbParticipants - $nbParticipations) / $nbParticipants * 100;
        } else {
            $pourcentageParticipants = 0; // Éviter une division par zéro
        }

        // Stocker le pourcentage de participation dans le tableau
        $participationPercentages[$campagneId] = $pourcentageParticipants;
    }
}

// Utiliser $participationPercentages comme nécessaire, par exemple, pour l'afficher dans votre vue Twig


        return $this->render('dash/index.html.twig', [
            'mostVisitedCampagnes' => $formattedMostVisitedCampagnes,
            'mostParticipatedCampagnes' => $formattedMostParticipatedCampagnes,
            'participationPercentages' => $participationPercentages,
            'campagneNames' => $campagneNames, // Ajout de la variable campagneNames


        ]);
    }
}
