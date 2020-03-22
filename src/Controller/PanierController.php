<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Form\PanierType;
use App\Form\ProduitType;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/{_locale}")
 */
class PanierController extends AbstractController
{
    /** 
    *@Route("/", name="home")
    */
    public function index(PanierRepository $repository){
        $panier = $repository->findAll();
        return $this->render('panier/index.html.twig',[
            'paniers'=>$panier
        ]);
    }

     /** 
    *@Route("/delete/{id}", name="deleteProdPanier")
    */
    public function deleteProdPanier(PanierRepository $repository, Panier $panier, Request $request){
       if($panier != null){
            $em = $this->getDoctrine()->getManager();
            $em->remove($panier);
            $em->flush();

            $this->addFlash('warning', 'Produit supprimé');
        }
        else{
            $this->addFlash('danger', 'Impossible de supprimer le produit');
        }
        
        return $this->redirectToRoute('home');
    }
    
}
