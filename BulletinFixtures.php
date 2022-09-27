<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Entity\Bulletin;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class BulletinFixtures extends Fixture // Fichier BulletinFixtures.php
{
    public function load(ObjectManager $manager): void
    {
        // Nous préparons une collection de Tags:
        $tagsNames = ["PHP", "Symfony", "Doctrine", "Twig", "Programmation", "Webapp", "Composer", "Entities", "MySQL", "PhpMyAdmin"];
        $tags = []; // tableau vide 
        foreach ($tagsNames as $tagName){
            // on crée une boucle foreach qui va parcourir notre tableau de noms de Tag, et pour chaque élément, nous allons initialiser un Tag et le ranger dans le tableau $tags
            $tag = new Tag;     
            $tag->setName($tagName);
            array_push($tags, $tag);
            $manager->persist($tag);
        }
        // Nous plaçons tous les intitulés de catégorie possible dans un tableau 
        $categoryNames = ["Général", "Divers", "Urgent"];
        // cette méthode load() nous permettra d'envoyer une quantité d'entrées vers notre table bulletin
        // Inutile de le renseigner tout de suite grâce au Constructeur
        for ($i = 0; $i < 30; $i++) {
            // On demande à faire persister $bulletin 
            $bulletin = new Bulletin("Titre #" . rand(1000, 9999), "Général");
            foreach ($tags as $tag){
                if(rand(1,10) > 8 ) $bulletin->addTag($tag); // 20% de chance de lier $tag à $bulletin 
            }
            // on renseigne certains éléments à nouveau grâce aux setters
            $bulletin->setTitle(("Bulletin #" . rand(1000, 9999)));
            $bulletin->setCategory($categoryNames[rand(0, (count($categoryNames) - 1))]);
            $manager->persist($bulletin);
            // On applique 
        }
        $manager->flush();
    }
}
