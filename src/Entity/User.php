<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
// Imports
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100), Assert\NotBlank()]
    private ?string $name = null;

    #[ORM\Column(length: 100), Assert\NotBlank()]
    private ?string $lastname = null;

    #[ORM\Column(length: 100), Assert\NotBlank(), Assert\Email()]
    private ?string $email = null;

    #[ORM\Column(length: 100)]
    private ?string $password = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false), Assert\NotBlank()]
    private ?Rol $rol = null;

    #[ORM\Column (options : ["default" => false])]
    private ?bool $isDelete = false;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRol(): ?rol
    {
        return $this->rol;
    }

    public function setRol(?rol $rol): static
    {
        $this->rol = $rol;

        return $this;
    }

    public function getIsDelete(): ?bool
    {
        return $this->isDelete;
    }

    public function setIsDelete(bool $isDelete): static
    {
        $this->isDelete = $isDelete;

        return $this;
    }

    // MÉTODOS OBLIGATORIOS DEL USERINTERFACE

    /*
    public function getRoles(): array{
    // Verificar si el usuario tiene un rol asignado
    if ($this->rol) {
        // Devolver un array con el nombre del rol del usuario
        return [$this->rol->getName()];
    }
        // Si el usuario no tiene un rol asignado, devolver un array vacío o un rol predeterminado
        return [];
    }*/
    public function getRoles(): array
    {
        return $this->getRol()->getCredencial();
    }

    public function eraseCredentials(): void {

    }

    public function getUserIdentifier(): string {
        
        return $this->email;
    }

    public function getFullName () {

        return $this->name . ' ' . $this->lastname;
    }
    

    
}
