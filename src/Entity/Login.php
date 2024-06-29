<?php

namespace App\Entity;

use App\Repository\LoginRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoginRepository::class)]
class Login
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(length: 255)]
    private ?string $img = null;

    #[ORM\Column(length: 255)]
    private ?string $About = null;

    #[ORM\OneToMany(mappedBy: 'details', targetEntity: Posts::class)]
    private Collection $Posts;

    #[ORM\Column(length: 255)]
    private ?string $gender = null;

    #[ORM\OneToMany(mappedBy: 'LoginComments', targetEntity: Comments::class)]
    private Collection $LoginComments;

    #[ORM\OneToMany(mappedBy: 'likeDislike', targetEntity: LikeDislike::class)]
    private Collection $userLike;

    #[ORM\OneToMany(mappedBy: 'fromRequest', targetEntity: FriendRequest::class)]
    private Collection $request;

    #[ORM\OneToMany(mappedBy: 'toRequest', targetEntity: FriendRequest::class)]
    private Collection $requestTo;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: FriendList::class)]
    private Collection $User;

    #[ORM\OneToMany(mappedBy: 'Friends', targetEntity: FriendList::class)]
    private Collection $Friends;

    public function __construct()
    {
        $this->Posts = new ArrayCollection();
        $this->LoginComments = new ArrayCollection();
        $this->userLike = new ArrayCollection();
        $this->request = new ArrayCollection();
        $this->requestTo = new ArrayCollection();
        $this->User = new ArrayCollection();
        $this->Friends = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->About;
    }

    public function setAbout(string $About): self
    {
        $this->About = $About;

        return $this;
    }

    /**
     * @return Collection<int, Posts>
     */
    public function getPosts(): Collection
    {
        return $this->Posts;
    }

    public function addPost(Posts $post): self
    {
        if (!$this->Posts->contains($post)) {
            $this->Posts->add($post);
            $post->setDetails($this);
        }

        return $this;
    }

    public function removePost(Posts $post): self
    {
        if ($this->Posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getDetails() === $this) {
                $post->setDetails(null);
            }
        }

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getLoginComments(): Collection
    {
        return $this->LoginComments;
    }

    public function addLoginComment(Comments $loginComment): self
    {
        if (!$this->LoginComments->contains($loginComment)) {
            $this->LoginComments->add($loginComment);
            $loginComment->setLoginComments($this);
        }

        return $this;
    }

    public function removeLoginComment(Comments $loginComment): self
    {
        if ($this->LoginComments->removeElement($loginComment)) {
            // set the owning side to null (unless already changed)
            if ($loginComment->getLoginComments() === $this) {
                $loginComment->setLoginComments(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LikeDislike>
     */
    public function getUserLike(): Collection
    {
        return $this->userLike;
    }

    public function addUserLike(LikeDislike $userLike): self
    {
        if (!$this->userLike->contains($userLike)) {
            $this->userLike->add($userLike);
            $userLike->setLikeDislike($this);
        }

        return $this;
    }

    public function removeUserLike(LikeDislike $userLike): self
    {
        if ($this->userLike->removeElement($userLike)) {
            // set the owning side to null (unless already changed)
            if ($userLike->getLikeDislike() === $this) {
                $userLike->setLikeDislike(null);
            }
        }

        return $this;
    }
    public function setVal(array $dataSetAccount) {
        $this->setFirstName($dataSetAccount['firstName']);
        $this->setLastName($dataSetAccount['lastName']);
        $this->setGender($dataSetAccount['gender']);
        $this->setImg($dataSetAccount['image']);
        $this->setAbout($dataSetAccount['about']);
        $this->setEmail($dataSetAccount['email']);
        $this->setPassword($dataSetAccount['pass']);
        $this->setStatus('1');
    }

    /**
     * @return Collection<int, FriendRequest>
     */
    public function getRequest(): Collection
    {
        return $this->request;
    }

    public function addRequest(FriendRequest $request): self
    {
        if (!$this->request->contains($request)) {
            $this->request->add($request);
            $request->setFromRequest($this);
        }

        return $this;
    }

    public function removeRequest(FriendRequest $request): self
    {
        if ($this->request->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getFromRequest() === $this) {
                $request->setFromRequest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendRequest>
     */
    public function getRequestTo(): Collection
    {
        return $this->requestTo;
    }

    public function addRequestTo(FriendRequest $requestTo): self
    {
        if (!$this->requestTo->contains($requestTo)) {
            $this->requestTo->add($requestTo);
            $requestTo->setToRequest($this);
        }

        return $this;
    }

    public function removeRequestTo(FriendRequest $requestTo): self
    {
        if ($this->requestTo->removeElement($requestTo)) {
            // set the owning side to null (unless already changed)
            if ($requestTo->getToRequest() === $this) {
                $requestTo->setToRequest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendList>
     */
    public function getUser(): Collection
    {
        return $this->User;
    }

    public function addUser(FriendList $user): self
    {
        if (!$this->User->contains($user)) {
            $this->User->add($user);
            $user->setUser($this);
        }

        return $this;
    }

    public function removeUser(FriendList $user): self
    {
        if ($this->User->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getUser() === $this) {
                $user->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendList>
     */
    public function getFriends(): Collection
    {
        return $this->Friends;
    }

    public function addFriend(FriendList $friend): self
    {
        if (!$this->Friends->contains($friend)) {
            $this->Friends->add($friend);
            $friend->setFriends($this);
        }

        return $this;
    }

    public function removeFriend(FriendList $friend): self
    {
        if ($this->Friends->removeElement($friend)) {
            // set the owning side to null (unless already changed)
            if ($friend->getFriends() === $this) {
                $friend->setFriends(null);
            }
        }

        return $this;
    }
}
