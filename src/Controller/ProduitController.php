<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Form\PanierType;
use App\Form\ProduitType;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/{_locale}")
 */
class ProduitController extends AbstractController
{
   /**
     * @Route("/produit", name="produit")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $fichier =$form->get('ImageUpload')->getData();
            if($fichier){
                $nomFichier = uniqid() . '.' . $fichier->guessExtension();

                try{
                    
                    $fichier->move(
                        $this->getParameter('upload_dir'),
                        $nomFichier
                    );
                }

                catch(FileException $e){
                    $this->addFlash('danger', "Impossible d'uploader le fichier");
                    return $this->redirectToRoute('produit');
                }

                $produit->setImage($nomFichier);
                
            }

            $em->persist($produit);
            $em->flush();
            $this->addFlash("success", "le produit à été ajouté");
        }

        $produits = $em->getRepository(Produit::class)->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'ajout_produit' => $form->createView()
        ]);
    }


    /**
     * @Route("/produit/add/{id}", name="panier_add")
     */
    public function add($id, ProduitRepository $repository, Panier $panier=null, Request $request){
        $produit=$repository->find($id);
        if($panier==null){
            $panier= new Panier;
        }
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PanierType::class, $panier);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $panier->setDateAjout(new \DateTime);
                $panier->setEtat(true);
                $produit->addPanier($panier);
                $em->persist($panier);
                $em->flush();
                $this->addFlash('succes', 'Produit ajouté');
    }
    return $this->render('produit/produit.html.twig', [
        'produit'=>$produit,
        'form_panier'=>$form->createView()
    ]);
    }
    /**
     * @Route("/produit/delete/{id}", name="delete_produit")
     */
    public function delete(Produit $produit=null){
        if($produit != null){
            $em = $this->getDoctrine()->getManager();
            $em->remove($produit);
            $em->flush();

            $this->addFlash('warning', 'Produit supprimé');
        }
        else{
            $this->addFlash('danger', 'Impossible de supprimer le produit');
        }
        
        return $this->redirectToRoute('produit');
    }
}
