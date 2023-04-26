<?php

declare (strict_types = 1);

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractFOSRestController
{
    protected $responseArray = [
        "success" => true,
    ];
    protected function buildForm(string $type, $data = null, array $options = []): FormInterface
    {

        return $this->container->get('form.factory')->createNamed('', $type, $data, $options);
    }

    protected function respond($data = "", int $statusCode = Response::HTTP_OK): Response
    {
        if (empty($data)) {
            $data = $this->responseArray;
        }

        return $this->handleView($this->view($data, $statusCode));
    }

    // Burada formun validasyon sebebi ile mi yoksa farklı bir sebepten mi submit edilmediğini anlıyoruz.
    protected function checkFormErrorReason($form)
    {
        // İki aşamalı yapılmasının sebebi , form objesi submitted olmadan, validasyon kontrolü yapıalamaz.

        //  Eğer form submitted olmadıysa , direkt gövde sorunludur.
        if (!$form->isSubmitted()) {
            return 0;
        } else {
            //  Eğer form submitted ise ve validasyon işlemi gerçekleşmemiş ise , validasyonda hata vardır.
            if (!$form->isValid()) {
                return 1;
            }
        }
        return -1;
    }

}
