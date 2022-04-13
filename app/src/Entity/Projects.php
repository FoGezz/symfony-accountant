<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Projects
 *
 * @ORM\Table(name="projects", indexes={@ORM\Index(name="IDX_5C93B3A4AE80F5DF", columns={"department_id"})})
 * @ORM\Entity
 */
class Projects
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="projects_id_seq", allocationSize=1, initialValue=1)
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false)
     */
    private string $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cost", type="integer", nullable=true)
     */
    private int|null $cost;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_beg", type="date", nullable=true)
     */
    private \DateTime|null $dateBeg;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_end", type="date", nullable=true)
     */
    private \DateTime|null $dateEnd;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_end_real", type="date", nullable=true)
     */
    private \DateTime|null $dateEndReal;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(?int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getDateBeg(): ?\DateTimeInterface
    {
        return $this->dateBeg;
    }

    public function setDateBeg(?\DateTimeInterface $dateBeg): self
    {
        $this->dateBeg = $dateBeg;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getDateEndReal(): ?\DateTimeInterface
    {
        return $this->dateEndReal;
    }

    public function setDateEndReal(?\DateTimeInterface $dateEndReal): self
    {
        $this->dateEndReal = $dateEndReal;

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
