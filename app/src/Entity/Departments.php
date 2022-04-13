<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Departments
 *
 * @ORM\Table(name="departments")
 * @ORM\Entity
 */
class Departments
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="departments_id_seq", allocationSize=1, initialValue=1)
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private string $name;

    /**
     * @var Collection<Employees>
     * @ORM\ManyToMany(targetEntity="Employees", mappedBy="departments")
     * @ORM\JoinTable(name="departments_employees",
     *     joinColumns={@ORM\JoinColumn(name="department_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="employee_id", referencedColumnName="id", unique=true)}
     * )
     */
    private Collection $employees;


    #[Pure] public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    #[Pure] public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @param Collection $employees
     * @return Departments
     */
    public function setEmployees(Collection $employees): Departments
    {
        $this->employees = $employees;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

}
