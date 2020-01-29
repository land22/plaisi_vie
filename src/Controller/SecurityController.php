<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
         $this->addFlash('success_registration', 'Vous faite parti de liste de no clients vous pouvez déjà vous connectez pour commander!');
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

   /**
   *@Route("/user_list", name="user_list")
   * @Security("is_granted('ROLE_ADMIN')")
   */
   public function user_list (){
      $repository = $this->getDoctrine()
    ->getRepository(User::class);
      $user = $repository->findAll();
      return $this->render('security/user_list.html.twig',
  ['users'=>$user]
   );
   } 
   /**
   *@Route("/update_user/{id}", name="update_user")
   * @Security("is_granted('ROLE_ADMIN')")
   */
   public function update_user ($id, Request $request){
      $repository = $this->getDoctrine()
    ->getRepository(User::class);
      $user = $repository->find($id);
       $data = array();
       $data[] = $request->request->get('roles');
      
    $manager = $this->getDoctrine()->getManager();
      $user->setRoles($data);
      $manager->flush();
         return $this->redirectToRoute('user_list');
   } 


   /**
	*@Route("/deconnexion", name="security_logout")
	*/
   public function logout (){ 
   }
}
