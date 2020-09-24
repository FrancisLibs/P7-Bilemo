<?php

namespace App\Entity;

use App\Entity\User;
use Swagger\Annotations as SWG;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @UniqueEntity("email", message="This email is not available")
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"customer:list", "customer:show"})
     * @SWG\Property(description="The unique identifier of the customer.")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="First name is required")
     * @Assert\Length(
     *      min = 3,
     *      max = 255,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters",
     * )
     * @Groups({"customer:list", "customer:show"})
     * @SWG\Property(type="string", maxLength=255, minLength=3)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Name is required")
     * @Assert\Length(
     *      min = 3,
     *      max = 255,
     *      minMessage = "Your name must be at least {{ limit }} characters long",
     *      maxMessage = "Your name cannot be longer than {{ limit }} characters",
     * )
     * @Groups({"customer:list", "customer:show"})
     * @SWG\Property(type="string", maxLength=255, minLength=3)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Email is required")
     * @Assert\Email(message = "The email is not a valid email.")
     * @Groups({"customer:list", "customer:show"})
     * @SWG\Property(type="string", maxLength=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Company is required")
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "Your company name must be at least {{ limit }} characters long",
     *      maxMessage = "Your company name cannot be longer than {{ limit }} characters"
     * )
     * @Groups({"customer:list", "customer:show"})
     * @SWG\Property(type="string", maxLength=255, minLength=2)
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="customers")
     * @Assert\NotBlank(message="User is required")
     * @Groups({"customer:list", "customer:show"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

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
