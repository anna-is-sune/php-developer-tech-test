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
            ]);

            $this->render('results.twig', [
                'matchedCompanies'  => $matchedCompanies,
            ]);
        } else {
            session_start();
            $_SESSION['errorMessages'] = ['Invalid Postcode provided: ' . $postcode];
            $posted = [];
            foreach ($_POST as $param => $value) {
                switch ($param) {
                    case 'first_name':
                    case 'surname':
                    case 'email':
                    case 'phone':
                    case 'address_line_1':
                    case 'address_line_2':
                    case 'address_line_3':
                    case 'address_line_4':
                    case 'town':
                    case 'county':
                    case 'postcode':
                    case 'type':
                    case 'bedrooms':
                    case 'property_value':
                    case 'additional_information':
                        $posted[$param] = $value;
                        break;
                }
            }

            $_SESSION['posted'] = $posted;

            header('Location: form.php');
        }
    }
}