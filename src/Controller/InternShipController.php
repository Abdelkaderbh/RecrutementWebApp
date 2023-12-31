<?php

namespace App\Controller;

use App\Entity\InternShip;
use App\Form\InternShipType;
use App\Repository\InternShipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/internship')]
class InternShipController extends AbstractController
{
    #[Route('/', name: 'app_intern_ship_index', methods: ['GET'])]
    public function index(InternShipRepository $internShipRepository): Response
    {
        return $this->render('internship/index.html.twig', [
            'internships' => $internShipRepository->findAll(),
        ]);
    }

    #[Route('/newIntership', name: 'app_intern_ship_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $internShip = new InternShip();
        $form = $this->createForm(InternShipType::class, $internShip);
        $form->handleRequest($request);
        $recruiter = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($internShip);
            $internShip->setRecruiter($recruiter);
            $entityManager->flush();

            return $this->redirectToRoute('get_all_jobs', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('/home_recruteur/addInternship.html.twig', [
            'internShip' => $internShip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_intern_ship_show', methods: ['GET'])]
    public function show(InternShip $internShip): Response
    {
        return $this->render('internship/show.html.twig', [
            'internShip' => $internShip,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_intern_ship_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InternShip $internShip, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InternShipType::class, $internShip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_intern_ship_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('intern_ship/edit.html.twig', [
            'intern_ship' => $internShip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_intern_ship_delete', methods: ['POST'])]
    public function delete(Request $request, InternShip $internShip, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$internShip->getId(), $request->request->get('_token'))) {
            $entityManager->remove($internShip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_intern_ship_index', [], Response::HTTP_SEE_OTHER);
    }
}
