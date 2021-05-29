<?php

namespace App\Controller;

use App\Repository\ContactsRepository;
use App\Entity\Contacts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
* @Route("/api/contact", name="api_contact")
*/
class ContactsController extends AbstractController
{
    /**
     * @Route("/", name="all_contact", methods={"GET"})
     */
    public function contacts(ContactsRepository $contactsRepository): Response
    {

        $contacts = $contactsRepository->findAll(); // on met tous les contatcs dans cette variable.

        return $this->Json($contacts, 200); // on renvoie la variable en format JSon avec un code 200 qui veut dire pas d'erreur.
    }

    /**
     * @Route("/{id}", name="contact", methods={"GET"})
     */
    public function trouvercontact(ContactsRepository $contactsRepository, $id): Response
    {

        $contact = $contactsRepository->find($id); // on met le contact dont l'id est celui donnée en paramètre dans une variable.

        if($contact == NULL){ // si il n'ya pas de contact associé à cet id ;
            return $this->Json("Contact pas trouvé" , 400 ); // on renvoie en format JSon un texte d'erreur avec le code 400 qui veut dire qu'une erreur est détectée
        }
    
        return $this->Json($contact, 200 ); // Si un contact est trouvé on le renvoie en format JSon avec un code 200 qui veut dire pas d'erreur.
    }
    
    /**
     * @Route("/add", name="add_contact", methods={"POST"})
     */
    public function ajoutcontact(Request $request ,EntityManagerInterface $em, SerializerInterface $serializer): Response
    {
        $inputs = $request->getContent(); //on récupère les données saisies par l'utilisateur.
        $contact = $serializer->deserialize($inputs , Contacts::class , 'json'); // on met le texte (Json) donnée par l'utilisateur en type Contacts
        $erreurs = []; // va contenir les erreurs détectées et rien si pas d'erreur.

        //on vérifie si un des champs est nul :
        if(empty($contact->getPrenom()) || empty($contact->getNom()) || empty($contact->getEmail()) || empty($contact->getTelephone()) || empty($contact->getAdresse()) || empty($contact->getAge())){
            array_push($erreurs , "Les champs ne doivent pas être nuls") ; // si oui, on l'écrit dans erreurs
        }
        //on vérifie si le mail n'est pas en bonne format 
        if(!empty($contact->getEmail()) && strpos($contact->getEmail(), "@") == false){
            array_push($erreurs , "Adresse e-mail non valide") ; // si oui, on l'écrit dans erreurs
        }
        //on vérifie si l'âge n'est pas en bonne format
        if(!empty($contact->getAge()) && (!is_numeric($contact->getAge())  || ($contact->getAge() > 110) || ($contact->getAge() < 1)) ){
            array_push($erreurs , "L'âge doit être un nombre compris entre 1 et 110") ;// si oui, on l'écrit dans erreurs
        }
        if(sizeof($erreurs) > 0){ // si on a rentrés des erreurs
            return $this->Json($erreurs , 400 ); // on les renvoie en format JSON avec le code 400 qui veut dire qu'une erreur est détectée et on arrête.
        }

        // Si pas d'erreur on rentre les données dans la BD.
        $em->persist($contact);
        $em->flush();

        return $this->Json($contact , 201 ); // et puis on renvoie le contact rentré en format JSon avec un code 200 qui veut dire pas d'erreur.
    }

    /**
     * @Route("/update/{id}", name="update_contact", methods={"PUT"})
     */
    public function updatecontact(Request $request ,EntityManagerInterface $em , $id, ContactsRepository $contactsRepository ): Response
    {
        $data = json_decode($request->getContent(), true); // les données à changer et à mettre
        $contact = $contactsRepository->find($id); // on cherche le contact qu'on veut modifier
        if(!empty($data['prenom'])){ $contact->setPrenom($data['prenom']); } // on verifie si on veut changer le prenom
        if(!empty($data['nom'])){ $contact->setNom($data['nom']); } // on verifie si on veut changer le nom
        if(!empty($data['Email'])){ $contact->setEmail($data['Email']); } // on verifie si on veut changer l' email
        if(!empty($data['telephone'])){ $contact->setTelephone($data['telephone']); } // on verifie si on veut changer le telephone
        if(!empty($data['adresse'])){ $contact->setAdresse($data['adresse']); } // on verifie si on veut changer l'adresse
        if(!empty($data['age'])){ $contact->setAge($data['age']); } // on verifie si on veut changer l'age

        $erreurs = [];// va contenir les erreurs détectées et rien si pas d'erreur.

        //On vérifie si au moins un champ est renseigné
        if(empty($data['prenom']) && empty($data['nom']) && empty($data['Email']) && empty($data['telephone']) && empty($data['adresse']) && empty($data['age'])){
            array_push($erreurs , "Il devrait y avoir au moins un champ à changer.") ; // sinon, on l'écrit dans erreurs
        }
        //on vérifie si le mail n'est pas en bonne format 
        if(!empty($data['Email']) && strpos($data['Email'], "@") == false){
            array_push($erreurs , "adresse e-mail invalide") ; //si oui, on l'écrit dans erreurs
        }
        //on vérifie si l'âge n'est pas en bonne format
        if(!empty($data['age']) && (!is_numeric($data['age'])  || ($data['age'] > 110) || ($data['age'] < 1)) ){
            array_push($erreurs , "L'âge doit être un nombre compris entre 1 et 110") ;//si oui, on l'écrit dans erreurs
        }
        if(sizeof($erreurs) > 0){// si on a rentrés des erreurs
            return $this->Json($erreurs , 400 );// on les renvoie en format JSON avec le code 400 qui veut dire qu'une erreur est détectée et on arrête.
       
        }

        // Si pas d'erreur on rentre les données dans la BD.
        $em->persist($contact);
        $em->flush();

        return $this->Json($contact , 201 ); // et puis on renvoie le contact à jour en format JSon avec un code 201 qui veut dire pas d'erreur.
    }

    /**
     * @Route("/delete/{id}", name="delete_contact", methods={"DELETE"})
     */
    public function deletecontact(EntityManagerInterface $em , ContactsRepository $contactsRepository , $id): Response
    {
        $contact = $contactsRepository->find($id); // on cherche le contact qu'on veut supprimer
        
        if($contact == NULL){
            return $this->Json("contact non trouvé" , 400 ); // s'il n'existe pas on renvoie une erreur en format JSON avec le code 400 qui veut dire qu'une erreur est détectée et on arrête.
        }
        
        // Sinon on supprime les données dans la BD.
        $em->remove($contact);
        $em->flush();

        return $this->Json($contact , 201 );  // et puis on renvoie le contact supprimé en format JSon avec un code 201 qui veut dire pas d'erreur.
    }

}
