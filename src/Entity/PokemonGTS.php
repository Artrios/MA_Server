<?php

namespace App\Entity;

use App\Repository\PokemonGTSRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonGTSRepository::class)]
class PokemonGTS
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?int $checksum = null;

    #[ORM\Column]
    private ?int $pid = null;

    #[ORM\Column(type: Types::BINARY)]
    private $pokemon;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $dex_id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $gender = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $level = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $requested_dex_id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $requested_gender = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $min_level = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $max_level = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $trainer_gender = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $trainer_id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $secret_id = null;

    #[ORM\Column(length: 7)]
    private ?string $otname = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $country = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $region = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $trainer_class = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $is_exchanged = null;

    #[ORM\Column]
    private ?int $version = null;

    #[ORM\Column]
    private ?int $rom_hack_id = null;

    #[ORM\Column]
    private ?int $rom_hack_ver = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $language = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChecksum(): ?int
    {
        return $this->checksum;
    }

    public function setChecksum(int $checksum): static
    {
        $this->checksum = $checksum;

        return $this;
    }

    public function getPid(): ?int
    {
        return $this->pid;
    }

    public function setPid(int $pid): static
    {
        $this->pid = $pid;

        return $this;
    }

    public function getPokemon()
    {
        return $this->pokemon;
    }

    public function setPokemon($pokemon): static
    {
        $this->pokemon = $pokemon;

        return $this;
    }

    public function getDexId(): ?int
    {
        return $this->dex_id;
    }

    public function setDexId(int $dex_id): static
    {
        $this->dex_id = $dex_id;

        return $this;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getRequestedDexId(): ?int
    {
        return $this->requested_dex_id;
    }

    public function setRequestedDexId(int $requested_dex_id): static
    {
        $this->requested_dex_id = $requested_dex_id;

        return $this;
    }

    public function getRequestedGender()
    {
        return $this->requested_gender;
    }

    public function setRequestedGender($requested_gender): static
    {
        $this->requested_gender = $requested_gender;

        return $this;
    }

    public function getMinLevel()
    {
        return $this->min_level;
    }

    public function setMinLevel($min_level): static
    {
        $this->min_level = $min_level;

        return $this;
    }

    public function getMaxLevel()
    {
        return $this->max_level;
    }

    public function setMaxLevel($max_level): static
    {
        $this->max_level = $max_level;

        return $this;
    }

    public function getTrainerGender()
    {
        return $this->trainer_gender;
    }

    public function setTrainerGender($trainer_gender): static
    {
        $this->trainer_gender = $trainer_gender;

        return $this;
    }

    public function getTrainerId(): ?int
    {
        return $this->trainer_id;
    }

    public function setTrainerId(int $trainer_id): static
    {
        $this->trainer_id = $trainer_id;

        return $this;
    }

    public function getSecretId(): ?int
    {
        return $this->secret_id;
    }

    public function setSecretId(int $secret_id): static
    {
        $this->secret_id = $secret_id;

        return $this;
    }

    public function getOtname(): ?string
    {
        return $this->otname;
    }

    public function setOtname(string $otname): static
    {
        $this->otname = $otname;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion($region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getTrainerClass()
    {
        return $this->trainer_class;
    }

    public function setTrainerClass($trainer_class): static
    {
        $this->trainer_class = $trainer_class;

        return $this;
    }

    public function getIsExchanged(): ?int
    {
        return $this->is_exchanged;
    }

    public function setIsExchanged(int $is_exchanged): static
    {
        $this->is_exchanged = $is_exchanged;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(int $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getRomHackId(): ?int
    {
        return $this->rom_hack_id;
    }

    public function setRomHackId(int $rom_hack_id): static
    {
        $this->rom_hack_id = $rom_hack_id;

        return $this;
    }

    public function getRomHackVer(): ?int
    {
        return $this->rom_hack_ver;
    }

    public function setRomHackVer(int $rom_hack_ver): static
    {
        $this->rom_hack_ver = $rom_hack_ver;

        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage($language): static
    {
        $this->language = $language;

        return $this;
    }
}
