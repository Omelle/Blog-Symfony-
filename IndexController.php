<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Bulletin;
use App\Form\BulletinType;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // Ceci est la page d'accueil
        // Le corps de notre méthode peut contenir différentes instructions pouvant influencer la reponse rendue à l'utilisateur

        // Nous récupérons ici l'Entity Manager et le Repository de Bulletin afin de pouvoir récupérer

        // on crée une collection de bulletin sous la variable $bulletins 
        // $bulletins = [];
        // Nous créons une boucle où nous ajoutons des bulletins créés à  notre tableau 
        // on créer un bulletin 

        // on renseigne notre instance de Bulletin 
        $entityManager = $doctrine->getManager();
        $bulletinRepository = $entityManager->getRepository(Bulletin::class);
        // Nous recupérons la list des category
        $categories = $bulletinRepository->findEachCategory();
        // Nous recuperons la liste des bulletins 
        $bulletins = $bulletinRepository->findAll();
        $bulletins = array_reverse($bulletins);
        // Nous inverson l'ordre de nos bulletins pour commencer à partir du plus récent:
        //on envoie à Twig notre collection de bulletins
        return $this->render('index/index.html.twig', [
            'categories' => $categories,
            'bulletins' => $bulletins,
        ]);
    }
    #[Route('/category/{categoryName}', name: 'index_category')]
    public function indexCategory(string $categoryName, ManagerRegistry $doctrine): Response
    {
        // Cette méthode nous rends les Bulletin dont la cétagorie correspond à la valeur entrée dnas notre barre d'adresse en tant que "CategoryName"

        // Afin de pouvoir communiquee avec notre Base de données , nous avons besoin de l'Entity Manager ainsi que du Repository de Bulletin 
        $entityManager = $doctrine->getManager();
        $bulletinRepository = $entityManager->getRepository(Bulletin::class);
        // Nous recupérons la list des category
        $categories = $bulletinRepository->findEachCategory();
        // Nous vérifions si la catégorie mentionnée dans notre varre d'adresse existe parmi nos Bulletins. Si non , nous retournons à l'index. 
        // Nous récupérons les bulletins dont la catégorie correspond: 
        $bulletins = $bulletinRepository->findBy(['category' => $categoryName], ['id' => 'DESC']);
        if (empty($bulletins)) {
            return $this->redirectToRoute('app_index');
        }
        // Nous transmettons, s'ils existent, les bulletins reçus sur index.html.twig
        return $this->render('index/index.html.twig', [
            'bulletins' => $bulletins,
            'categories' => $categories,
        ]);
    }


    #[Route('/square-display/{squareValue}', name: 'square_display')]
    public function displaySquare(string $squareValue = "test"): Response
    // le parametre de route {squareValue} est une valeur modulable de l'adresse, laquelle est passée au sein de notre méthode via le paramètre $squareValue
    {
        $colors = ['red', 'blue', 'green', 'black', 'orange'];
        // Notre variable est au pluriel car il s'agit d'un tableau plusieurs exemplaires du qualificatif de la variable en question. 
        $selectedColor = $colors[rand(0, (count($colors) - 1))];
        // Nous séléctionons une valeur au hasard de notre tableau allant de la clef 0 à la clef maximale (nombre total d'entréess, moins un ) 

        switch (strtolower($squareValue)) {
            case 'rouge':
                $selectedColor = 'red';
                break;
            case 'vert':
                $selectedColor = 'green';
                break;
            case 'jaune':
                $selectedColor = 'yellow';
                break;
            case '':
                $selectedColor = 'gray';
                break;
            default: // Si auncune propositon n'est retenue , la valeur par defaut est "black"
                $selectedColor = 'black';
        }
        return new Response('<h1 style="font-size:72px;">' . $squareValue . '</h1><div style="width:500px; height:500px; background-color:' . $selectedColor . ';"></div>');
    }

    #[Route('/cheatsheet/', name: 'index_cheatsheet')]
    public function cheatsheet(): Response
    {
        return $this->render('index/cheatsheet.html.twig', ['cheastheet_var' => true]);
    }

    #[Route('bulletin/create', name: 'bulletin_create')]
    public function createBuletin(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        // Cette méthode a pour objectif de créer un nouveau bulletin dont les différentes  informations sont passées de l'utilisateur à l'application par l'intermédiaire d'un formulaire 

        // Creer une instance d'Entity Bulletin que  nous allons lier à notre formulaire 
        $bulletin = new Bulletin;
        $bulletin->clearFields(); // ici on vide les champs de notre Bulletin 
        $bulletinRepository = $entityManager->getRepository(Bulletin::class);
        $categories = $bulletinRepository->findEachCategory();

        $bulletinForm = $this->createForm(BulletinType::class, $bulletin);
        $bulletinForm->handleRequest($request);
        // Si notre formulaire est rempli et valide , nous portons le Bulletin vers notre BDD
        if ($bulletinForm->isSubmitted() && $bulletinForm->isValid()) {
            $entityManager->persist($bulletin); // Etant donnée que le Bulletin est lié au formulaire, il est automatiquement rempli avec les valeurs du formulaire
            $entityManager->flush();
            // Nous retournons à l'accueil 
            return $this->redirectToRoute('app_index');
        }
        // Nous transmettons notre formaulaire de bulletin à Twig 
        return $this->render('index/dataform.html.twig', [
            'category' => $categories,
            'formName' => 'Creation de Bulletin',
            'dataForm' => $bulletinForm->createView(),
        ]); // createView() prepare l'affichage du form ]
    }

    #[Route('/bulletin/update/{bulletinId}', name: 'bulletin_update')]
    public function updateBulletin(int $bulletinId, Request $request, ManagerRegistry $doctrine): Response
    {
        // Cette méthode nous permet de modifier grâce à un formulaire le contenu d'un Bulletin identifié via son ID transmis via notre URL 

        $entityManager = $doctrine->getManager();
        $bulletinRepository = $entityManager->getRepository(Bulletin::class);
        $bulletin = $bulletinRepository->find($bulletinId);

        $categories = $bulletinRepository->findEachCategory();

        // Si le bulletin n'existe pas,nous retournons à l'index
        if (!$bulletin) {
            return $this->redirectToRoute('app_index');
        }
        $bulletinForm = $this->createForm(BulletinType::class, $bulletin);
        if ($bulletinForm->isSubmitted() &&  $bulletinForm->isValid()) {
            $entityManager->persist($bulletin); // Etant donnée que le Bulletin est lié au formulaire, il est automatiquement rempli avec les valeurs du formulaire
            $entityManager->flush();
            // Nous retournons à l'accueil 
            return $this->redirectToRoute('app_index');
        }
        return $this->render('index/dataform.html.twig', [
            'category' => $categories,
            'formName' => 'Modification de Bulletin',
            'dataForm' => $bulletinForm->createView(),
        ]);
    }

    #[Route('/bulletin/display', name: 'bulletin_display')]
    public function displayBulletin(int $bulletinId, ManagerRegistry $doctrine): Response
    { // Cette methode affiche un Bulletin dont l'ID est spécifié dans la barre d'adresse
        //Afin de mener une recherche dans notre BDD, nous avons besoin de l'Entity Manager ainsi que du Repository Bulletin 
        $entityManager = $doctrine->getManager();
        $bulletinRepository = $entityManager->getRepository(Bulletin::class);
        $bulletin = $bulletinRepository->find($bulletinId);
        $categories = $bulletinRepository->findEachCategory();
        if (!$bulletin) {
            return $this->redirectToRoute('app_index');
        }
        // Si nous avons notre Bulletin , nous le transmettons a index.html.twig
        return $this->render('index/index.html.twig', [
            'category' => $categories,
            'bulletins' => [$bulletin],
        ]);
    }

    #[Route('/bulletin/delete/{bulletinId}', name: 'bulletin_delete')]
    public function deleteBulletin(ManagerRegistry $doctrine, int $bulletinId): Response
    {  // Cette méthode permet de supprimer un Bulletin dont l'ID nous est transmis via l'URL 

        // Afin de récupérer le Bulletin désiré, nous avons beoin de l'Entity Manager et du Repository de Bulletin

        $entityManager = $doctrine->getManager();
        $bulletinRepository = $entityManager->getRepository(Bulletin::class);
        // Nous utilisons la méthode find() du Repository, laquelle permet de récupérer un élément de la bDD en utilisant l'ID , passé en paramètre. Si aucun élément ne correspond à l'ID indiqyé , la valeur renvoyé sera null 
        $bulletin = $bulletinRepository->find($bulletinId);
        // Si le bulletin n'existe pas,nous retournons à l'index
        if (!$bulletin) {
            return $this->redirectToRoute('app_index');
        }
        // S notre Bulletin  est récupéré, nous procédons à sa suppression avant de revenir sur notre page d'accueil 
        $entityManager->remove($bulletin);
        $entityManager->flush();
        return $this->redirectToRoute('app_index');
    }
    // Création d'une nouvelle méthode 
    #[Route('/bulletin/generate', name: 'bulletin_generate')]
    public function generateBulletin(ManagerRegistry $doctrine): Response
    { // cette méthode génère un Bulletin automatique, le fait persister dans notre BDD avant de revenir sur notre méthode index ( route: app_index)
        // Tout d'abord, nous recuperons l' Entity Manager pour envoyer notre bulletin vers la BDD 

        $entityManager = $doctrine->getManager();
        // Nous créons ensuite notre Bulletin dont nous renseignons les attributs
        $bulletin = new Bulletin("Bulletin Généré-" . uniqid(), "Généré");
        // Une demande de persistance que nous appliquons 
        // On demande la persistance de $bulletin
        $entityManager->persist($bulletin);
        $entityManager->flush(); // On applique les (la) demandé(s)
        return $this->redirectToRoute('app_index'); // Une fois que notre bulletin a été persisté , nous retournons vers notre méthode index().
    }

    // Creer une nouvelle méthode 
    #[Route('/tag/{tagId}', name: 'index_tag')]
    public function indexTag(string $tagId, ManagerRegistry $doctrine): Response
    // Cette méthode renvoie la liste de tous les Bulletins liés au Tag dont le nom est affiché au sein de notre URL 
    // Afin de pouvoir récupérer le Tag, nous utilisons l'Entity Manager et le Repository de TAG 
    {
        $entityManager = $doctrine->getManager();
        $tagRepository = $entityManager->getRepository(Tag::class);
        $bulletinRepository = $entityManager->getRepository(Bulletin::class);
        $categories = $bulletinRepository->findEachCategory();
        // on récupère le Tag selon le nom indiqué. Si la recherche n'aboutit pas, nous revenons à l'index. 
        $tag = $tagRepository->findOneBy(['name' => $tagId]);
        if (!$tag) {

            return $this->redirectToRoute('app_index');
        }
        // Notre liste de Bulletin lié au Tag 
        $bulletins = $tag->getBulletins();
        // On envoie cette liste à notre index.html.twig
        return $this->render('index/index.html.twig', ['bulletins' => $bulletins,]);
    }
}
