<?php

namespace App\Controller;

use App\Service\CompanyMatcher;

class FormController extends Controller
{
    public function index()
    {
        $this->render('form.twig');
    }

    public function submit()
    {
        $matcher = new CompanyMatcher($this->db());

        $postcode = $_POST['postcode'];

        if (preg_match('/(([A-Z]{1,2})[0-9]{1,2}[A-Z]?)(\s+([0-9][A-Z]{2}))?/', $postcode, $matches) && count($matches) > 2) {
            $matchedCompanies = $matcher->match([
                'postcodes' => $matches[2],
                'bedrooms'  => $_POST['bedrooms'],
                'type'      => $_POST['type'],
            ])->deductCredits()->results();

            $this->render('results.twig', [
                'matchedCompanies'  => $matchedCompanies,
            ]);
        } else {
            header('Location: form.php');
        }
    }
}