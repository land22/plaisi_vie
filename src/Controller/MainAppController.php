<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\MenuRepository;
use App\Entity\Menu;
use App\Form\MenuType;

class MainAppController extends Controller
{
    /**
     * @Route("/", name="main_app")
     */
    public function index()
    {
        return $this->render('main_app/index.html.twig', [
            'controller_name' => 'MainAppController',
        ]);
    }
    /**
     * @Route("/create_menu", name="create_menu")
     */
    public function create_menu(Request $request)
    { 
    	$menu = new Menu();
   	$form_menu = $this->createForm(MenuType::class, $menu);
   	$form_menu->handleRequest($request);
   	if($form_menu->isSubmitted() and $form_menu->isValid() ) {
   		/** @var UploadedFile $brochureFile */
            $menuFile = $form_menu->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($menuFile) {
                $originalFilename = pathinfo($menuFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$menuFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $menuFile->move(
                        $this->getParameter('menu_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
                $menu->setImage($newFilename);
                $menu->setValide(0);
                $menu->setDatePub(new \DateTime());
   		$manager = $this->getDoctrine()->getManager();
   		$manager->persist($menu);
   		$manager->flush();
   		return $this->redirectToRoute('liste_menu');
   	}
   	return $this->render('main_app/create_menu.html.twig', [
     'form_menu' =>  $form_menu->createView()
   	]) ;  	
       
    }
    /**
     * @Route("/liste_menu", name="liste_menu")
     */
    public function liste_menu()
    {
    	$repository = $this->getDoctrine()
    ->getRepository(Menu::class);
    	$repos_menu = $repository->findAll();
        return $this->render('main_app/liste_menu.html.twig', [
            'menus' => $repos_menu,
        ]);
    }
}
