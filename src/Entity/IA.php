<?php

namespace App\Entity;

use App\Repository\IARepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

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
     * @var Collection<int, Chatbox>
     */
    #[ORM\OneToMany(targetEntity: Chatbox::class, mappedBy: 'ia')]
    private Collection $chatboxes;

    /**
     * Nom de l'IA
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * Clé API OpenAI
     */
    #[ORM\Column(length: 255)]
    private ?string $apiKey = null;

    /**
     * Modèle OpenAI à utiliser
     */
    #[ORM\Column(length: 50)]
    private ?string $model = 'gpt-3.5-turbo';

    /**
     * Température pour la génération
     */
    #[ORM\Column(type: Types::FLOAT)]
    private float $temperature = 0.7;

    /**
     * Contexte de conversation par défaut
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $defaultContext = null;

    /**
     * Paramètres additionnels en JSON
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $additionalParams = [];

    /**
     * Initialise une nouvelle instance d'IA
     */
    public function __construct()
    {
        $this->chatboxes = new ArrayCollection();
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
     * @return Collection<int, Chatbox>
     */
    public function getChatboxes(): Collection
    {
        return $this->chatboxes;
    }

    /**
     * Ajoute une chatbox à la gestion de l'IA
     * 
     * @param Chatbox $chatbox La chatbox à ajouter
     */
    public function addChatbox(Chatbox $chatbox): static
    {
        if (!$this->chatboxes->contains($chatbox)) {
            $this->chatboxes->add($chatbox);
            $chatbox->setIa($this);
        }

        return $this;
    }

    /**
     * Retire une chatbox de la gestion de l'IA
     * 
     * @param Chatbox $chatbox La chatbox à retirer
     */
    public function removeChatbox(Chatbox $chatbox): static
    {
        if ($this->chatboxes->removeElement($chatbox)) {
            // set the owning side to null (unless already changed)
            if ($chatbox->getIa() === $this) {
                $chatbox->setIa(null);
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
        $apiKey = $this->apiKey;
        $url = 'https://api.openai.com/v1/engines/' . $this->model . '/completions';

        $data = [
            'prompt' => $message,
            'max_tokens' => 150,
            'temperature' => $this->temperature,
            'context' => $this->defaultContext,
            'additional_params' => $this->additionalParams,
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

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): static
    {
        $this->temperature = $temperature;
        return $this;
    }

    public function getDefaultContext(): ?string
    {
        return $this->defaultContext;
    }

    public function setDefaultContext(?string $defaultContext): static
    {
        $this->defaultContext = $defaultContext;
        return $this;
    }

    public function getAdditionalParams(): array
    {
        return $this->additionalParams;
    }

    public function setAdditionalParams(?array $additionalParams): static
    {
        $this->additionalParams = $additionalParams ?? [];
        return $this;
    }
}
