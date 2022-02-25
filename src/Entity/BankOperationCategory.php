<?php

namespace App\Entity;

use App\Repository\BankOperationCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BankOperationCategoryRepository::class)
 */
class BankOperationCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=BankOperation::class, mappedBy="bankOperationCategory")
     */
    private $bankOperations;

    public function __construct()
    {
        $this->bankOperations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->label;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|BankOperation[]
     */
    public function getBankOperations(): Collection
    {
        return $this->bankOperations;
    }

    public function addBankOperation(BankOperation $bankOperation): self
    {
        if (!$this->bankOperations->contains($bankOperation)) {
            $this->bankOperations[] = $bankOperation;
            $bankOperation->setBankOperationCategory($this);
        }

        return $this;
    }

    public function removeBankOperation(BankOperation $bankOperation): self
    {
        if ($this->bankOperations->removeElement($bankOperation)) {
            // set the owning side to null (unless already changed)
            if ($bankOperation->getBankOperationCategory() === $this) {
                $bankOperation->setBankOperationCategory(null);
            }
        }

        return $this;
    }
}
