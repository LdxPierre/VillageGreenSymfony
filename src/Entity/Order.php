<?php

namespace App\Entity;

use App\Entity\Address;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderRepository;

use function PHPUnit\Framework\throwException;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $shippingDate = null;

    #[ORM\Column(length: 100)]
    private ?string $shippingName = null;

    #[ORM\Column(length: 100)]
    private ?string $shippingAddress = null;

    #[ORM\Column(length: 70)]
    private ?string $shippingCity = null;

    #[ORM\Column(length: 15)]
    private ?string $shippingZipCode = null;

    #[ORM\Column(length: 70)]
    private ?string $shippingCountry = null;

    #[ORM\Column(length: 100)]
    private ?string $billingName = null;

    #[ORM\Column(length: 100)]
    private ?string $billingAddress = null;

    #[ORM\Column(length: 70)]
    private ?string $billingCity = null;

    #[ORM\Column(length: 15)]
    private ?string $billingZipCode = null;

    #[ORM\Column(length: 70)]
    private ?string $billingCountry = null;

    #[ORM\OneToMany(mappedBy: 'orderParent', targetEntity: OrderItem::class)]
    private Collection $orderItems;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getShippingDate(): ?\DateTimeInterface
    {
        return $this->shippingDate;
    }

    public function setShippingDate(\DateTimeInterface $shippingDate): self
    {
        $this->shippingDate = $shippingDate;

        return $this;
    }

    public function getShippingName(): ?string
    {
        return $this->shippingName;
    }

    public function setShippingName(string $shippingName): self
    {
        $this->shippingName = $shippingName;

        return $this;
    }

    public function getShippingAddress(): ?string
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(string $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getShippingCity(): ?string
    {
        return $this->shippingCity;
    }

    public function setShippingCity(string $shippingCity): self
    {
        $this->shippingCity = $shippingCity;

        return $this;
    }

    public function getShippingZipCode(): ?string
    {
        return $this->shippingZipCode;
    }

    public function setShippingZipCode(string $shippingZipCode): self
    {
        $this->shippingZipCode = $shippingZipCode;

        return $this;
    }

    public function getShippingCountry(): ?string
    {
        return $this->shippingCountry;
    }

    public function setShippingCountry(string $shippingCountry): self
    {
        $this->shippingCountry = $shippingCountry;

        return $this;
    }

    public function getBillingName(): ?string
    {
        return $this->billingName;
    }

    public function setBillingName(string $billingName): self
    {
        $this->billingName = $billingName;

        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(string $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(string $billingCity): self
    {
        $this->billingCity = $billingCity;

        return $this;
    }

    public function getBillingZipCode(): ?string
    {
        return $this->billingZipCode;
    }

    public function setBillingZipCode(string $billingZipCode): self
    {
        $this->billingZipCode = $billingZipCode;

        return $this;
    }

    public function getBillingCountry(): ?string
    {
        return $this->billingCountry;
    }

    public function setBillingCountry(string $billingCountry): self
    {
        $this->billingCountry = $billingCountry;

        return $this;
    }

    /**
     * set instance of Address as shippingName, address, zipcode, city and coutry
     */
    public function setShipping(Address $address)
    {
        $this->shippingName = $address->getFullname();
        $this->shippingAddress = $address->getAddress();
        $this->shippingZipCode = $address->getZipcode();
        $this->shippingCity = $address->getCity();
        $this->shippingCountry = $address->getCountry();
    }

    /**
     * set instance of Address as billingName, address, zipcode, city and coutry
     */
    public function setBilling(Address $address)
    {
        $this->billingName = $address->getFullname();
        $this->billingAddress = $address->getAddress();
        $this->billingZipCode = $address->getZipcode();
        $this->billingCity = $address->getCity();
        $this->billingCountry = $address->getCountry();
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrderParent($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrderParent() === $this) {
                $orderItem->setOrderParent(null);
            }
        }

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
}
