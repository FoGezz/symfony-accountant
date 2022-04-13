<?php

namespace App\Controller;

use App\Entity\DepartmentsEmployees;
use App\Form\DepartmentsEmployeesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/departments/employees')]
class DepartmentsEmployeesController extends AbstractController
{
    #[Route('/', name: 'app_departments_employees_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $departmentsEmployees = $entityManager
            ->getRepository(DepartmentsEmployees::class)
            ->findAll();

        return $this->render('departments_employees/index.html.twig', [
            'departments_employees' => $departmentsEmployees,
        ]);
    }

    #[Route('/new', name: 'app_departments_employees_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $departmentsEmployee = new DepartmentsEmployees();
        $form = $this->createForm(DepartmentsEmployeesType::class, $departmentsEmployee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($departmentsEmployee);
            $entityManager->flush();

            return $this->redirectToRoute('app_departments_employees_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('departments_employees/new.html.twig', [
            'departments_employee' => $departmentsEmployee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_departments_employees_show', methods: ['GET'])]
    public function show(DepartmentsEmployees $departmentsEmployee): Response
    {
        return $this->render('departments_employees/show.html.twig', [
            'departments_employee' => $departmentsEmployee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_departments_employees_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DepartmentsEmployees $departmentsEmployee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepartmentsEmployeesType::class, $departmentsEmployee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_departments_employees_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('departments_employees/edit.html.twig', [
            'departments_employee' => $departmentsEmployee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_departments_employees_delete', methods: ['POST'])]
    public function delete(Request $request, DepartmentsEmployees $departmentsEmployee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$departmentsEmployee->getId(), $request->request->get('_token'))) {
            $entityManager->remove($departmentsEmployee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_departments_employees_index', [], Response::HTTP_SEE_OTHER);
    }
}
