<?php

namespace App\Controller\Admin;

use App\Entity\Departments;
use App\Entity\Employees;
use App\Entity\Projects;
use App\Service\ProfitCalculator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private ProfitCalculator $profitCalculator,
    )
    {}

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route('/admin')]
    public function index(): Response
    {
        $incompleteProjects = $this->profitCalculator->getProfitForProjects();
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
         return $this->render('admin/index.html.twig', [
             'incompleteProjects' => $incompleteProjects,
         ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Бухгалтерия');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenuItems = [];

        if (class_exists(LogoutUrlGenerator::class)) {
            $userMenuItems[] = MenuItem::section();
        }

        if ($this->isGranted(Permission::EA_EXIT_IMPERSONATION)) {
            $userMenuItems[] = MenuItem::linkToExitImpersonation('__ea__user.exit_impersonation', 'fa-user-lock');
        }
        return parent::configureUserMenu($user)->setMenuItems($userMenuItems);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Отделы', 'fas fa-list', Departments::class);
        yield MenuItem::linkToCrud('Сотрудники', 'fas fa-list', Employees::class);
        yield MenuItem::linkToCrud('Проекты', 'fas fa-list', Projects::class);
    }
}
