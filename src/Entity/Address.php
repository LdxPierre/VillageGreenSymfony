<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Veuillez saisir un nom complet')]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: 'Le nom complet doit contenir entre 5 et 100 catactères',
        maxMessage: 'Le nom complet doit contenir entre 5 et 100 caractères'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zéèçàâẑêŷûîôŝĝĥĵŵĉäëÿüïöḧẅẍùA-Z]+(?:[- ]?[a-zéèçàâẑêŷûîôŝĝĥĵŵĉäëÿüïöḧẅẍùA-Z]+){0,4}$/',
        message: 'Votre nom complet ne peut pas contenir de caractères spéciaux ou chiffres'
    )]
    #[ORM\Column(length: 100)]
    private ?string $fullname = null;

    #[Assert\NotBlank(message: 'Veuillez saisir un numéro de téléphone')]
    #[Assert\Length(
        min: 8,
        max: 20,
        minMessage: 'Veuillez saisir un numéro de téléphone valide',
        maxMessage: 'Veuillez saisir un numéro de téléphone valide'
    )]
    #[Assert\Regex(
        pattern: '/\+?\d{1,4}?[-.\s]?\(?\d{1,3}?\)?[-.\s]?\d{1,4}[-.\s]?\d{1,4}[-.\s]?\d{1,9}/',
        message: 'Veuillez saisir un numéro de téléphone valide'
    )]
    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[Assert\NotBlank(message: 'Veuillez saisir un numéro de voie')]
    #[Assert\Length(
        max: 10,
        maxMessage: 'Le numéro de voie ne peut pas dépasser 10 caractères'
    )]
    #[Assert\Regex(
        pattern: '/^[\d]{1,4}(?:[ ]{1}[a-zA-Z]{1,5})?$/',
        message: 'Veuillez saisir un numéro de voie valide',
    )]
    #[ORM\Column(length: 10)]
    private ?string $line = null;

    #[Assert\NotBlank(message: 'Veuillez saisir un nom de rue')]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: 'Le nom de rue doit contenir au moins 5 caractères',
        maxMessage: 'Le nom de rue ne doit pas dépasser 100 caractères',
    )]
    #[Assert\Regex(
        pattern: '/^[a-zéèçàâẑêŷûîôŝĝĥĵŵĉäëÿüïöḧẅẍùA-Z]{1,50}(?:(?:, |,|[- ])[a-zéèçàâẑêŷûîôŝĝĥĵŵĉäëÿüïöḧẅẍùA-Z]+)*$/',
        message: 'Veuillez saisir un nom de rue valide'
    )]
    #[ORM\Column(length: 50)]
    private ?string $street = null;

    #[Assert\NotBlank(message: 'Veuillez saisir un code postal')]
    #[Assert\Length(
        min: 5,
        max: 15,
        minMessage: 'Le code postal doit contenir au moins 5 caractères',
        maxMessage: 'Le code postal ne peut pas dépasser 15 caractères',
    )]
    #[Assert\Regex(
        pattern: '/^[\d\w]*(?:[-| ]*[\d\w]+){0,2}$/',
        message: 'Veuillez saisir un code postal valide',
    )]
    #[ORM\Column(length: 15)]
    private ?string $zipcode = null;

    #[Assert\NotBlank(message: 'Veuillez saisir un nom de ville')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Le nom de la ville ne doit pas dépasser 50 caractères'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zéèçàâẑêŷûîôŝĝĥĵŵĉäëÿüïöḧẅẍùA-Z]*(?:[ -]?[\']?[a-zéèçàâẑêŷûîôŝĝĥĵŵĉäëÿüïöḧẅẍùA-Z]+){0,3}$/',
        message: 'Veuillez saisir un nom de ville valide'
    )]
    #[ORM\Column(length: 50)]
    private ?string $city = null;

    #[Assert\Length(max: 144, maxMessage: 'Les instructions ne peuvent pas dépasser 144 caractères')]
    #[Assert\Regex(
        pattern: '/[@&~"\'([-|`_\\)\]=+}$£¤`µ%!§:\/<>]+/',
        match: false,
        message: 'Les instructions ne doivent pas contenir de caracères spéciaux'
    )]
    #[ORM\Column(type: Types::TEXT, nullable: true, length: 144)]
    private ?string $instructions = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Assert\NotBlank(message: 'Veuillez saisir un nom de pays')]
    #[Assert\Length(
        min: 4,
        minMessage: 'Le nom du pays doit contenir au moins 4 caractères',
        max: 70,
        maxMessage: 'Le nom du pays ne doit pas dépasser 70 caractères'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zéèçàâẑêŷûîôŝĝĥĵŵĉäëÿüïöḧẅẍùA-Z]+(?:[ -]{1}[a-zéèçàâẑêŷûîôŝĝĥĵŵĉäëÿüïöḧẅẍùA-Z]+){0,4}$/',
        message: 'Veuillez saisir un nom de pays valide'
    )]
    #[ORM\Column(length: 70)]
    private ?string $country = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLine(): ?string
    {
        return $this->line;
    }

    public function setLine(string $line): self
    {
        $this->line = $line;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(?string $instructions): self
    {
        $this->instructions = $instructions;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }
}
