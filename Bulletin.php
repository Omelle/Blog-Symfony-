<?php

namespace App\Entity;

use App\Repository\BulletinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BulletinRepository::class)]
class Bulletin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $category;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $creationDate;

    #[ORM\ManyToMany(targetEntity:'App\Entity\Tag', inversedBy: 'bulletins')]
    #[ORM\JoinColumn(nullable: true )]
    private $tags; 


    public function __construct($title = '', $category = 'Inconnue', $content = '', $contents = '')
    {
        if (!$title) // si title équivaut à '', on génère automatiquement son contenu 
            $this->title = "Titre" . uniqid();
        else {
            $this->$title = $title;
        }
        // Contenu
        if (!$content) {
            $this->content = $this->generateContent();
        } else {
            $this->content = $content;
        }
        // on renseigne l'attribut category 
        $this->category = $category;

        // la date de création est toujours automatiquement renseignée 
        $this->creationDate = new \DateTime("now");
        $this->tags = new ArrayCollection();
    }
    public function generateContent(): string
    { // les extraits qui vont constituer notre attribut content. L'extrait "0" est celui par leqquel chacun de nos contenus commencent . 
        $contents = [
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis diam odio, luctus in ornare at, tincidunt volutpat mauris. Vestibulum ac ipsum non est mollis maximus in at justo. Vivamus a urna sit amet arcu vehicula maximus et eu orci.",
            "Aenean commodo condimentum urna. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nunc sodales nulla mauris, sed pharetra dolor interdum non.",
            "Cras feugiat rhoncus venenatis. Morbi vitae vehicula velit, scelerisque ullamcorper leo. Fusce luctus magna metus, at pellentesque nibh tincidunt vel. Etiam dictum fermentum dolor.",
            "Duis id faucibus tellus. Nunc efficitur porta tortor ut porta. Cras porta sollicitudin gravida. Pellentesque euismod neque id pharetra fermentum. Nullam dictum libero sed placerat ornare. Mauris nec risus bibendum, vestibulum dui et, aliquam ante. Cras et feugiat velit.,
            Vivamus elementum leo facilisis justo varius, non consectetur lorem blandit. Duis bibendum, nisl sed fermentum vulputate, odio lacus porta orci, ac ullamcorper massa magna eu sem. Proin dictum enim sed neque eleifend sodales. Integer tempus maximus lorem id venenatis.",
            "Pellentesque id neque sit amet orci eleifend pharetra. Nunc cursus vulputate sapien, vitae scelerisque lorem laoreet a. Donec ultricies urna mollis, imperdiet nisl non, gravida elit. Nam non libero sit amet erat sagittis rhoncus. Quisque et molestie mi.",
            "Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nullam eros enim, posuere sit amet feugiat eget, finibus rutrum nunc. Mauris in tincidunt purus. Fusce sit amet pellentesque urna, a viverra erat. Donec id mattis elit, nec dapibus urna.",
            "Cras vel ultricies metus. Curabitur mauris erat, pharetra ut arcu in, vestibulum pulvinar dui. Fusce sed nisi ornare massa euismod vestibulum. Suspendisse viverra enim viverra velit varius, et tristique eros cursus. Donec porta sit amet sapien non vulputate.",
        ];

        // on prepare une variable laqeulle contiendra le texte qui devra rendu par notre méthode 
        $lorem = "";

        // On ajoute a $lorem le premier element de notre tableau par lequel tous nos $content doivent commencer 
        $lorem .= $contents[0];
        // On ajoute de nouveaux éléments à notre chaine de caractère $lorem via à une boucle for 
        for ($i = 0; $i < rand(2, 5); $i++) {
            // on décide dd'un espace ou d'un retour à la ligne 
            if (rand(0, 10) > 7) // 30% de chances d'un retour à la ligne . 
            {
                $lorem .= "
            ";
            } else {
                $lorem .= "";
            }
            $lorem .= " " . $contents[rand(1, (count($contents) - 1))];
        }
        // on retourne $lorem
        return $lorem;
    }

    public function getCategoryType(): string
    {
        // Cette méthode rend un code CSS selon la catégorie de notre bulletin 
        switch($this->category) {
            case "Général":
                return "info"; // le return met fin à la fonction
                break;
            case "Divers":
                return "warning"; // "esthétique", le break ici est inutile 
                break;
            case "Urgent":
                return "danger";
                break;
            case "Généré":
                return "success";
                break;
            default:
                return "secondary";
        }
    }

    public function clearFields(): void{
        // Cette méthode vide les attribut title, content et category de notre objet Bulletin 

        $this->title = null;
        $this->category = null;
        $this->content = null; 
    }
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
