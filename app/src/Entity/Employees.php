<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Employees
 *
 * @ORM\Table(name="employees")
 * @ORM\Entity
 */
class Employees
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="employees_id_seq", allocationSize=1, initialValue=1)
     */
    private int $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", length=20, nullable=true)
     */
    private string|null $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pather_name", type="string", length=20, nullable=true)
     */
    private string|null $patherName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=20, nullable=true)
     */
    private string|null $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="position", type="string", length=50, nullable=true)
     */
    private string|null $position;

    /**
     * @var int
     *
     * @ORM\Column(name="salary", type="smallint", nullable=true)
     */
    private int $salary;

    /**
     * @var Collection<Departments>
     * @ORM\ManyToMany(targetEntity="Departments", inversedBy="employees")
     * @ORM\JoinTable(name="departments_employees",
     *      joinColumns={@ORM\JoinColumn(name="employee_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="department_id", referencedColumnName="id", unique=true)}
     * )
     *
     */
    private Collection $departments;

    #[Pure] public function __construct()
    {
        $this->departments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPatherName(): ?string
    {
        return $this->patherName;
    }

    public function setPatherName(?string $patherName): self
    {
        $this->patherName = $patherName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(int $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    #[Pure] public function __toString(): string
    {
        return $this->getLastName() . ' ' . $this->getFirstName() . ' ' . $this->getPatherName();
    }

    /**
     * @param Collection $departments
     * @return Employees
     */
    public function setDepartments(Collection $departments): Employees
    {
        $this->departments = $departments;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }


}
