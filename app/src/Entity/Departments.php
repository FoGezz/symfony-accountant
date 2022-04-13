<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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


}
