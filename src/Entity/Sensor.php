<?php

namespace App\Entity;

use App\Repository\SensorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SensorRepository::class)]
#[UniqueEntity('ip')]
class Sensor
{
	#[ORM\Id]
	#[ORM\Column(type: "uuid", unique: true)]
	#[ORM\GeneratedValue(strategy: "CUSTOM")]
	#[ORM\CustomIdGenerator(class: UuidGenerator::class)]
	protected UuidInterface|string $id;

	#[Assert\Ip]
	#[ORM\Column(length: 255, unique: true)]
    private ?string $ip = null;

    #[ORM\OneToMany(mappedBy: 'sensor', targetEntity: Temperature::class)]
    private Collection $temperatures;

    public function __construct()
    {
        $this->temperatures = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return Collection<int, Temperature>
     */
    public function getTemperatures(): Collection
    {
        return $this->temperatures;
    }

    public function addTemperature(Temperature $temperature): self
    {
        if (!$this->temperatures->contains($temperature)) {
            $this->temperatures->add($temperature);
            $temperature->setSensor($this);
        }

        return $this;
    }

    public function removeTemperature(Temperature $temperature): self
    {
        if ($this->temperatures->removeElement($temperature)) {
            // set the owning side to null (unless already changed)
            if ($temperature->getSensor() === $this) {
                $temperature->setSensor(null);
            }
        }

        return $this;
    }
}
