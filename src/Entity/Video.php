<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\VideoRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(
        min: 3,
        minMessage: 'Le titre doit contenir minimum {{ limit }} caractères',
        max: 30,
        maxMessage: 'Le titre doit contenir maximum {{ limit }} caractères'
    )]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Url(
        protocols: ['https'],
        message: 'URL invalide',
    )]
    #[Assert\Regex(
        pattern: '#(youtu)|(dai\.?ly)#',
        message: 'Les format d\'URL acceptés sont : https://youtu.be/videoID et https://dai.ly/videoID'
    )]
    private $url;

    #[ORM\ManyToOne(targetEntity: Trick::class, inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: false)]
    private $trick;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}
