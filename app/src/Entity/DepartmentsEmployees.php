<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DepartmentsEmployees
 *
 * @ORM\Table(name="departments_employees", indexes={@ORM\Index(name="IDX_E99173E18C03F15C", columns={"employee_id"}), @ORM\Index(name="IDX_E99173E1AE80F5DF", columns={"department_id"})})
 * @ORM\Entity
 */
class DepartmentsEmployees
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="departments_employees_id_seq", allocationSize=1, initialValue=1)
     */
    private int $id;

    /**
     * @var Employees
     *
     * @ORM\ManyToOne(targetEntity="Employees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     * })
     */
    private Employees $employee;

    /**
     * @var Departments
     *
     * @ORM\ManyToOne(targetEntity="Departments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     * })
     */
    private Departments $department;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employees
    {
        return $this->employee;
    }

    public function setEmployee(Employees $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function getDepartment(): ?Departments
    {
        return $this->department;
    }

    public function setDepartment(Departments $department): self
    {
        $this->department = $department;

        return $this;
    }


}
