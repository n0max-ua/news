<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    public const STATUS_CREATED = 1;
    public const STATUS_POSTED = 2;
    public const STATUS_DELETED = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $posted_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
        $this->status = self::STATUS_CREATED;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     * @return $this
     */
    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): self
    {
        if ($status == 2) {
            $this->status = self::STATUS_POSTED;
            $this->deleted_at = null;
            $this->posted_at = new DateTimeImmutable();
        } elseif ($status == 3) {
            $this->status = self::STATUS_DELETED;
            $this->deleted_at = new DateTimeImmutable();
        } else {
            $this->status = self::STATUS_CREATED;
            $this->created_at = new DateTimeImmutable();
            $this->deleted_at = null;
            $this->posted_at = null;
        }

        return $this;
    }

    /**
     * @return int[]
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_CREATED,
            self::STATUS_POSTED,
            self::STATUS_DELETED
        ];
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param DateTimeImmutable $created_at
     * @return $this
     */
    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getPostedAt(): ?DateTimeImmutable
    {
        return $this->posted_at;
    }

    /**
     * @param DateTimeImmutable|null $posted_at
     * @return $this
     */
    public function setPostedAt(?DateTimeImmutable $posted_at): self
    {
        $this->posted_at = $posted_at;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deleted_at;
    }

    /**
     * @param DateTimeImmutable|null $deleted_at
     * @return $this
     */
    public function setDeletedAt(?DateTimeImmutable $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return $this
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
