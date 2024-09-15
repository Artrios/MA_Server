<?php

namespace App\Entity;

use App\Repository\UserProfilesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProfilesRepository::class)]
class UserProfiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "integer")]
    private $Version;

    #[ORM\Column(type: "integer")]
    private $RomHackID;

    #[ORM\Column(type: "smallint")]
    private $RomHackVer;

    #[ORM\Column(type: "binary")]
    private $Language;

    #[ORM\Column(type: "binary")]
    private $Country;

    #[ORM\Column(type: "binary")]
    private $Region;

    #[ORM\Column(type: "integer")]
    private $TrainerID;

    #[ORM\Column(type: "string", length: 16)]
    private $TrainerName;

    #[ORM\Column(type: "bigint")]
    private $MAC;

    #[ORM\Column(type: "string", length: 56)]
    private $Email;

    #[ORM\Column(type: "integer")]
    private $Notify;

    #[ORM\Column(type: "smallint")]
    private $ClientSecret;

    #[ORM\Column(type: "smallint")]
    private $MailSecret;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?int
    {
        return $this->Version;
    }

    public function setVersion(int $Version): self
    {
        $this->Version = $Version;

        return $this;
    }

    public function getRomHackID(): ?int
    {
        return $this->RomHackID;
    }

    public function setRomHackID(int $RomHackID): self
    {
        $this->RomHackID = $RomHackID;

        return $this;
    }

    public function getRomHackVer(): ?int
    {
        return $this->RomHackVer;
    }

    public function setRomHackVer(int $RomHackVer): self
    {
        $this->RomHackVer = $RomHackVer;

        return $this;
    }

    public function getLanguage()
    {
        return $this->Language;
    }

    public function setLanguage($Language): self
    {
        $this->Language = $Language;

        return $this;
    }

    public function getCountry()
    {
        return $this->Country;
    }

    public function setCountry($Country): self
    {
        $this->Country = $Country;

        return $this;
    }

    public function getRegion()
    {
        return $this->Region;
    }

    public function setRegion($Region): self
    {
        $this->Region = $Region;

        return $this;
    }

    public function getTrainerID(): ?int
    {
        return $this->TrainerID;
    }

    public function setTrainerID(int $TrainerID): self
    {
        $this->TrainerID = $TrainerID;

        return $this;
    }

    public function getTrainerName(): ?string
    {
        return $this->TrainerName;
    }

    public function setTrainerName(string $TrainerName): self
    {
        $this->TrainerName = $TrainerName;

        return $this;
    }

    public function getMAC(): ?string
    {
        return $this->MAC;
    }

    public function setMAC(string $MAC): self
    {
        $this->MAC = $MAC;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getNotify(): ?int
    {
        return $this->Notify;
    }

    public function setNotify(int $Notify): self
    {
        $this->Notify = $Notify;

        return $this;
    }

    public function getClientSecret(): ?int
    {
        return $this->ClientSecret;
    }

    public function setClientSecret(int $ClientSecret): self
    {
        $this->ClientSecret = $ClientSecret;

        return $this;
    }

    public function getMailSecret(): ?int
    {
        return $this->MailSecret;
    }

    public function setMailSecret(int $MailSecret): self
    {
        $this->MailSecret = $MailSecret;

        return $this;
    }
}
