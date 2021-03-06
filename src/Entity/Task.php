<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Vous devez saisir un titre.')]
    private string $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Vous devez saisir du contenu.')]
    private string $content;

    #[ORM\Column(type: 'boolean')]
    private bool $isDone;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tasks')]
    private ?UserInterface $user;

    public function __construct() {
        $this->createdAt = new \Datetime();
        $this->isDone = false;
    }

    public function getId(): ?int {

        return $this->id;
    }

    public function getCreatedAt(): \DateTime {

        return $this->createdAt;
    }

    public function setCreatedAt(\Datetime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function getTitle(): string {

        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function getContent(): string {

        return $this->content;
    }

    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function isDone(): bool {

        return $this->isDone;
    }

    public function toggle(bool $flag): void {
        $this->isDone = $flag;
    }

    public function setAsDone():void {
        $this->isDone = true;
    }

    public function setAsUndone():void {
        $this->isDone = false;
    }

    public function getUser(): ?UserInterface {

        return $this->user;
    }

    public function setUser(?UserInterface $user): void {
        $this->user = $user;
    }
}
