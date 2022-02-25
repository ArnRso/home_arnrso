<?php

namespace App\Entity;

use App\Repository\BankOperationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BankOperationRepository::class)
 */
class BankOperation
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
    private $uniqId;

    /**
     * @ORM\Column(type="date")
     */
    private $operationDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $operationKind;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text")
     */
    private $rawData;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $affectedBudget;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bankOperations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=BankOperationCategory::class, inversedBy="bankOperations")
     */
    private $bankOperationCategory;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUniqId(): ?string
    {
        return $this->uniqId;
    }

    public function setUniqId(string $uniqId): self
    {
        $this->uniqId = $uniqId;

        return $this;
    }

    public function getOperationDate(): ?\DateTimeInterface
    {
        return $this->operationDate;
    }

    public function setOperationDate(\DateTimeInterface $operationDate): self
    {
        $this->operationDate = $operationDate;

        return $this;
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

    public function getOperationKind(): ?string
    {
        return $this->operationKind;
    }

    public function setOperationKind(string $operationKind): self
    {
        $this->operationKind = $operationKind;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getRawData(): ?string
    {
        return $this->rawData;
    }

    public function setRawData(string $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function getAffectedBudget(): ?string
    {
        return $this->affectedBudget;
    }

    public function setAffectedBudget(?string $affectedBudget): self
    {
        $this->affectedBudget = $affectedBudget;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBankOperationCategory(): ?BankOperationCategory
    {
        return $this->bankOperationCategory;
    }

    public function setBankOperationCategory(?BankOperationCategory $bankOperationCategory): self
    {
        $this->bankOperationCategory = $bankOperationCategory;

        return $this;
    }
}
