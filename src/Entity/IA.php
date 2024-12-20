<?php

namespace App\Entity;

use App\Repository\IARepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IARepository::class)]
class IA
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, ChatBox>
     */
    #[ORM\OneToMany(targetEntity: ChatBox::class, mappedBy: 'ia')]
    private Collection $chatBoxes;

    public function __construct()
    {
        $this->chatBoxes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ChatBox>
     */
    public function getChatBoxes(): Collection
    {
        return $this->chatBoxes;
    }

    public function addChatBox(ChatBox $chatBox): static
    {
        if (!$this->chatBoxes->contains($chatBox)) {
            $this->chatBoxes->add($chatBox);
            $chatBox->setIa($this);
        }

        return $this;
    }

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
}
