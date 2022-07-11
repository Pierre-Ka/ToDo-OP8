<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * @ORM\Table("user")
 * @ORM\Entity
 */


// Double unique entité ne marche pas ?!
// Pourtant c'est la doc: https://symfony.com/doc/current/reference/constraints/UniqueEntity.html#fields
//#[UniqueEntity(fields: ['email', 'username'], message: 'Ce champ doit être unique')]
#[UniqueEntity(fields: ['email'], message: 'Ce champ doit être unique')]
#[UniqueEntity(fields: ['username'], message: 'Ce champ doit être unique')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
//    #[ORM\Id]
//    #[ORM\GeneratedValue]
//    #[ORM\Column(type: 'integer')]
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

//    #[ORM\Column(type: 'string', length: 25, unique: true)]
//    #[Assert\NotBlank(message: 'Vous devez saisir un nom d\'utilisateur.')]
//    #[Assert\Length(min: 3, minMessage: 'Le nom n\'est pas assez long', max: 25, maxMessage: 'Le nom doit être inferieur à 25 caractères')]
    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank(message="Vous devez saisir un nom d'utilisateur.")
     */
    private string $username;

//    #[ORM\Column(type: 'string')]
    /**
     * @ORM\Column(type="string", length=64)
     */
    private string $password;

//    #[ORM\Column(type: 'string', length: 255, unique: true)]
//    #[Assert\NotBlank(message: 'Entrer votre email')]
//    #[Assert\Email(message:'Entrer un email valide')]
    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank(message="Vous devez saisir une adresse email.")
     * @Assert\Email(message="Le format de l'adresse n'est pas correcte.")
     */
    private string $email;

//    #[ORM\Column(type: 'json')]
    /**
     * @ORM\Column(type="json")
     * @var array<string>
     */
    private array $roles = [];

//    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Task::class)]
    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="user")
     */
    private ?Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername($username): void
    {
        $this->username = $username;
    }

    public function getSalt()
    {
        return null;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail($email) : void
    {
        $this->email = $email;
    }

    public function getRoles(): array
    {
        return (array) $this->roles;
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getTasks(): ?Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setUser($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            if ($task->getUser() === $this) {
                $task->setUser(null);
            }
        }

        return $this;
    }
}
