<?php

namespace App\Service;

class CompanyMatcher
{
    private $db;
    private $matches = [];

    public function __construct(\PDO $db) 
    {
        $this->db = $db;
    }

    public function match(array $filters)
    {
        $where = [];
        $params = [];
        if (array_key_exists('postcodes', $filters)) 
        {
            $where[] = 'cms.postcodes LIKE CONCAT(\'[%"\', :postcodes, \'"%]\')';
            $params['postcodes'] = $filters['postcodes'];
        }

        if (array_key_exists('bedrooms', $filters)) 
        {
            $where[] = 'cms.bedrooms LIKE CONCAT(\'[%"\', :bedrooms, \'"%]\')';
            $params['bedrooms'] = $filters['bedrooms'];
        }

        if (array_key_exists('type', $filters)) 
        {
            $where[] = 'cms.type = :type';
            $params['type'] = $filters['type'];
        }

        $sql = 'SELECT c.* FROM company_matching_settings cms INNER JOIN companies c ON cms.company_id = c.id WHERE c.active = true';
        foreach ($where as $condition) {
            $sql .= ' AND ' . $condition;
        }

        $sql .= ' ORDER BY rand() LIMIT 3'; 

        $companies = [];
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute($params)) {
            while ($company = $stmt->fetch(\PDO::FETCH_ASSOC)) 
            {
                $companies[] = $company;
            }
        }

        $this->matches = $companies;

        return $this;
    }

    public function pick(int $count)
    {
        
    }

    public function results(): array
    {
        return $this->matches;
    }

    public function deductCredits()
    {
        foreach ($this->matches as $match) {
            $stmt = $this->db->prepare('UPDATE companies SET credits = credits - 1 WHERE id = :id');
            $stmt->execute([
                'id' => $match['id']
            ]);
        }

        return $this;
    }
}
