<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;
use App\Form\UserType;


class SecurityController extends Controller
{
	/**
	*@Route("/inscription", name="security_registration")
	*/
   public function registration(Request $request, UserPasswordEncoderInterface $encoder){
    $user = new User();
   	$form = $this->createForm(UserType::class, $user);
   	$form->handleRequest($request);
   	if($form->isSubmitted() and $form->isValid() ) {
   		$manager = $this->getDoctrine()->getManager();
   		$hash = $encoder->encodePassword($user, $user->getPassword());
   		$user->setPassword($hash);
   		$manager->persist($user);
   		$manager->flush();
   		return $this->redirectToRoute('security_login');
   	}
   	return $this->render('security/registration.html.twig', [
     'form' =>  $form->createView()
   	]) ;  	

   }

   /**
	*@Route("/connexion", name="security_login")
	*/
   public function login (){
   	return $this->render('security/login.html.twig');
   }
}
