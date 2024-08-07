<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GTSController extends AbstractController
{
    /**
     * @Route("/g/t/s", name="app_g_t_s")
     */
    public function index(): Response
    {
        return $this->render('gts/index.html.twig', [
            'controller_name' => 'GTSController',
        ]);
    }

    /**
     * @Route("/worldexchange/info", name="ping_server")
     */
    public function info()
    {
        $online = 0x0001;
        $ar = pack('n', $online);

        return new Response($ar,Response::HTTP_OK);;
    }

    /**
     * @Route("/worldexchange/result", name="gts_result")
     */
    public function result()
    {
        doAuth();

        $result=check_result();

        if(strlen($result)==2){
            $ar = pack('n', $result);
        }
        elseif(strlen($result)==100){
            $ar = pack('n', $result);
        }
        else{
            return new Response($ar,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * @Route("/worldexchange/get", name="gts_get")
     */
    public function get_result()
    {
        doAuth();

        $result=get_deposited();

        if($result!=1){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }
}
