<?php


namespace App\Service;


use Doctrine\DBAL\Connection;

class ProfitCalculator
{
    public function __construct(
        private Connection $connection,
    )
    {}

    /**
     * Предполагаемая прибыль от каждого из незавершенных проектов
     * (прибыль вычисляется как стоимость проекта минус затраты,
     * затраты = зарплаты * время в месяцах)
     *
     *
     * @throws \Doctrine\DBAL\Exception
     * @return array {name: string, profit: int}
     */
    public function getProfitForProjects(): array
    {
        $sql = <<< QUERY
SELECT p.name, p.cost - sum(e.salary)::int * (date_end - date_beg) / 30 as income
FROM employees e
         JOIN departments_employees de ON e.id = de.employee_id
         JOIN departments d ON d.id = de.department_id
         JOIN projects p ON d.id = p.department_id
WHERE p.date_end_real IS NULL
GROUP BY p.id;
QUERY;

        return $this->connection->executeQuery($sql)->fetchAllAssociative();
    }

}