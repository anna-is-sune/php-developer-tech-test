<?php

namespace App\Controller;

use App\Service\CompanyMatcher;

class FormController extends Controller
{
    public function index()
    {
        session_start();

        $params = [];

        foreach ($_SESSION as $key => $value) {
            if (str_starts_with($key, 'post_')) {
                $params['postData'][substr($key, 5)] = $value;
            } elseif ('errorMessages' === $key) {
                $params['errorMessages'][] = $_SESSION['errorMessages'];
            }
        }

        $this->render('form.twig', $params);
    }

    public function submit()
    {
        $matcher = new CompanyMatcher($this->db());

        $postcode = $_POST['postcode'];

        session_start();
        unset($_SESSION['errorMessages']);

        foreach ($_POST as $key => $value) {
            $_SESSION['post_' . $key] = $value;
        }

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
            $_SESSION['errorMessages'] = 'Invalid postcode provided: ' . $postcode;

            header('Location: form');
        }
    }
}