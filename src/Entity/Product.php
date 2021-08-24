<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    const SET_PATH_TO_IMAGE_PRODUCT = '../var/uploads/product_images';
    const GET_PATH_TO_IMAGE_PRODUCT = '/uploads/product_images';

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    public function setFile(?UploadedFile $file = null): void
    {
        $this->file = $file;
    }

    public function getFile(): ?UploadedFile
    {
        // dd($this->file, 'getF1ile');
        return $this->file;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload(): void
    {
        // dd("upload",  $this->getFile()->move(
        //     self::SET_PATH_TO_IMAGE_PRODUCT,
        //     $this->getFile()->getFileName()
        // ));
        // the file property can be empty if the field is not required
         if (null === $this->getFile()) {
            return;
        }

       // we use the original file name here but you should
       // sanitize it at least to avoid any security issues

    //    dd($this->getFile());
       // move takes the target directory and target filename as params
       $this->getFile()->move(
           self::SET_PATH_TO_IMAGE_PRODUCT,
           $this->getFile()->getFileName()
       );

       // set the path property to the filename where you've saved the file
       $this->image = $this->getFile()->getFileName();

       // clean up the file property as you won't need it anymore
       $this->setFile(null);
   }

    /**
    * Lifecycle callback to upload the file to the server.
    */
    public function lifecycleFileUpload(): void
    {
        $this->upload();
    }

    // /**
    // * Updates the hash value to force the preUpdate and postUpdate events to fire.
    // */
    public function refreshUpdated(): void
    {
        // $this->setUpdated(new \DateTimeImmutable());
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    /**
    * @ORM\PrePersist
    */
    public function setCreatedAtValue() {
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    /**
    * @ORM\PreUpdate
    */
    public function setUpdatedAtValue() {
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=128, nullable=false)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    /**
     * @ORM\Column(type="integer", unique=true, nullable=true)
     */
    private $import_id;
    
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updated_at;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug($slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getActive(): bool
    {
        return (bool) $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getImport(): self
    {
        return $this->import_id;
    }

    public function setImport(float $import_id): self
    {
        $this->import_id = $import_id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
