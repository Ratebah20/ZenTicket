<?php

namespace App\Entity;

use App\Repository\IARepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une Intelligence Artificielle
 * 
 * Cette classe gère les IA qui peuvent interagir avec les tickets
 * et les chatbox pour assister les utilisateurs et les techniciens.
 */
#[ORM\Entity(repositoryClass: IARepository::class)]
class IA
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Collection des chatbox gérées par l'IA
     * 
     * @var Collection<int, ChatBox>
     */
    #[ORM\OneToMany(targetEntity: ChatBox::class, mappedBy: 'ia')]
    private Collection $chatBoxes;

    /**
     * Nom de l'IA
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * Initialise une nouvelle instance d'IA
     */
    public function __construct()
    {
        $this->chatBoxes = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant de l'IA
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère la collection des chatbox gérées par l'IA
     * 
     * @return Collection<int, ChatBox>
     */
    public function getChatBoxes(): Collection
    {
        return $this->chatBoxes;
    }

    /**
     * Ajoute une chatbox à la gestion de l'IA
     * 
     * @param ChatBox $chatBox La chatbox à ajouter
     */
    public function addChatBox(ChatBox $chatBox): static
    {
        if (!$this->chatBoxes->contains($chatBox)) {
            $this->chatBoxes->add($chatBox);
            $chatBox->setIa($this);
        }

        return $this;
    }

    /**
     * Retire une chatbox de la gestion de l'IA
     * 
     * @param ChatBox $chatBox La chatbox à retirer
     */
    public function removeChatBox(ChatBox $chatBox): static
    {
        if ($this->chatBoxes->removeElement($chatBox)) {
            // set the owning side to null (unless already changed)
            if ($chatBox->getIa() === $this) {
                $chatBox->setIa(null);
            }
        }

        return $this;
    }

    /**
     * Génère une réponse à un message donné en utilisant l'API OpenAI
     * 
     * @param string $message Le message auquel répondre
     * @return string La réponse générée
     * @throws \Exception En cas d'erreur de communication avec l'API
     */
    public function reponse(string $message): string
    {
        $apiKey = 'votre_clé_openai_ici';
        $url = 'https://api.openai.com/v1/engines/davinci-codex/completions';

        $data = [
            'prompt' => $message,
            'max_tokens' => 150,
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n" .
                             "Authorization: Bearer $apiKey\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ],
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            throw new \Exception("Erreur lors de la communication avec l'API OpenAI.");
        }

        $response = json_decode($result, true);
        return $response['choices'][0]['text'] ?? 'Aucune réponse disponible.';
    }

    /**
     * Récupère le nom de l'IA
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom de l'IA
     * 
     * @param string $nom Le nouveau nom de l'IA
     */
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
}
