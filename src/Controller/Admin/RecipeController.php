<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Container19hIdaB\getVichUploader_UploadHandlerService;
use ContainerHxmXM4l\getDoctrine_Orm_DefaultEntityManager_PropertyInfoExtractorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Route('/admin/recettes', name: 'admin.recipe.')]
class RecipeController extends AbstractController
{
  #[Route('/', name: 'index')]
  public function index(RecipeRepository $repository, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
  {

    $recipes = $repository->findWithDurationLowerThan(80);
    $entityManager->flush();
    return $this->render('admin/recipe/index.html.twig', [
      'recipes' => $recipes
    ]);
  }
  #[Route('/create', name: 'create')]
  public function create(Request $request, EntityManagerInterface $em)
  {
    $recipe = new Recipe();
    $form = $this->createForm(RecipeType::class, $recipe);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      // $recipe->setCreatedAt(new \DateTimeImmutable());
      // $recipe->setUpdatedAt(new \DateTimeImmutable());
      $em->persist($recipe);
      $em->flush();
      $this->addFlash('success', 'La recette a bien été créée');
      return $this->redirectToRoute('admin.recipe.index');
    }
    return  $this->render('admin/recipe/create.html.twig', ['form' => $form]);
  }


  #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
  public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, UploaderHelper $helper)
  {
    
    $form = $this->createForm(RecipeType::class, $recipe);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();
      $this->addFlash('success', 'La recette a bien été modifiée');
      return $this->redirectToRoute('admin.recipe.index');
    }
    return $this->render('admin/recipe/edit.html.twig', [
      'recipe' => $recipe,
      'form' => $form
    ]);
  }


  #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
  public function remove(recipe $recipe, EntityManagerInterface $em)
  {
    $em->remove($recipe);
    $em->flush();
    $this->addFlash('success', 'La recette a bien été supprimée');
    return $this->redirectToRoute('admin.recipe.index');
  }
}
