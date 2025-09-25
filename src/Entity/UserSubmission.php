<?php

namespace App\Entity;

use App\Repository\UserSubmissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSubmissionRepository::class)]
class UserSubmission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $puzzleWord = null;

    #[ORM\Column(type: Types::ARRAY,nullable: true)]
    private array $submittedWords = [];

    #[ORM\Column(nullable: true)]
    private ?int $score = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $remainingLetters = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPuzzleWord(): ?string
    {
        return $this->puzzleWord;
    }

    public function setPuzzleWord(string $puzzleWord): static
    {
        $this->puzzleWord = $puzzleWord;

        return $this;
    }

    public function getSubmittedWords(): array
    {
        return $this->submittedWords;
    }

    public function setSubmittedWords(array $submittedWords): static
    {
        $this->submittedWords = $submittedWords;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getRemainingLetters(): ?string
    {
        return $this->remainingLetters;
    }

    public function setRemainingLetters(?string $remainingLetters): static
    {
        $this->remainingLetters = $remainingLetters;

        return $this;
    }
}
