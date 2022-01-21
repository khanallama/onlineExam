<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
class Answer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $answers;

    #[ORM\ManyToOne(targetEntity: question::class, inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private $question;

    #[ORM\Column(type: 'integer')]
    private $answerId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswers(): ?string
    {
        return $this->answers;
    }

    public function setAnswers(string $answers): self
    {
        $this->answers = $answers;

        return $this;
    }

    public function getQuestion(): ?question
    {
        return $this->question;
    }

    public function setQuestion(?question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswerId(): ?int
    {
        return $this->answerId;
    }

    public function setAnswerId(int $answerId): self
    {
        $this->answerId = $answerId;

        return $this;
    }
}
