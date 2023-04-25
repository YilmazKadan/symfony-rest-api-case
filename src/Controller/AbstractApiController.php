<?php

declare (strict_types = 1);

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractFOSRestController
{
    protected $responseArray = [];
    protected function buildForm(string $type, $data = null, array $options = []): FormInterface
    {

        return $this->container->get('form.factory')->createNamed('', $type, $data, $options);
    }

    protected function respond($data = "", int $statusCode = Response::HTTP_OK): Response
    {   
        if (empty($data))
            $data = $this->responseArray;
        return $this->handleView($this->view($data, $statusCode));
    }
}
