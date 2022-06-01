<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\ImportType;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function home()
    {
        return $this->render('index.html.twig');
    }

    #[Route('/person', name: 'person_index', methods: ['GET'])]
    public function index(PersonRepository $personRepository): Response
    {
        return $this->render('person/index.html.twig', [
            'people' => $personRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/person/new', name: 'person_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($person);
            $entityManager->flush();

            return $this->redirectToRoute('person_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('person/new.html.twig', [
            'person' => $person,
            'form' => $form,
        ]);
    }

    #[Route('/person/import', name: 'person_import', methods: ['GET', 'POST'])]
    public function import(Request $request): Response
    {
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du fichier transmis
            $upload = $form['attachment']->getData();
            // Nouveau nom de fichier
            $file = sprintf('%s.%s', uniqid(), $upload->guessExtension());
            // Copie dans le dossier upload
            $upload->move(
                $this->getParameter('attachment_directory'),
                $file
            );
            // lecture du fichier
            $fileRead = new SplFileObject(sprintf('%s%s%s', $this->getParameter('attachment_directory'), \DIRECTORY_SEPARATOR, $file));
            $fileRead->setFlags(
                SplFileObject::READ_CSV |
                SplFileObject::SKIP_EMPTY |
                SplFileObject::DROP_NEW_LINE |
                SplFileObject::READ_AHEAD
            );
            $i = 0;
            while (false == $fileRead->eof()) {
                ++$i;
                if ($i <= 1) {
                    $fileArrayKey = $fileRead->fgetcsv();
                }
                if ($i >= 2) {
                    $fileArrayValue = $fileRead->fgetcsv();
                    dump(array_combine($fileArrayKey, $fileArrayValue));
                }
            }
        }

        return $this->renderForm('person/import.html.twig', [
            'form' => $form,
        ]);
    }
}
