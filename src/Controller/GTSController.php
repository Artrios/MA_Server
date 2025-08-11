<?php

namespace App\Controller;

use App\Service\MA_Helper;
use App\Service\GTS_Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GTSController extends AbstractController
{
    /**
     * @Route("/g/t/s", name="app_g_t_s")
     */
    #[Route("/g/t/s", name: "app_g_t_s")]
    public function index(): Response
    {
        return $this->render('gts/index.html.twig', [
            'controller_name' => 'GTSController',
        ]);
    }

    /**
     * Ping the server to determine it is available
     */
    #[Route("/pokemonrse/worldexchange/info", name: "ping_server")]
    public function info()
    {
        $online = 0x0001;
        $ar = pack('n', $online);

        return new Response($ar,Response::HTTP_OK);;
    }

    /**
     * Checks if the players deposited pokemon in the GTS has been traded or not
     * 0x0004 = The Pokemon has not been traded
     * 0x0005 = The player has no deposited pokemon
     * pkmn = If the Pokemon has been traded it returns the new Pokemon (100 bytes)
     */
    #[Route("/pokemonrse/worldexchange/result", name: "gts_result")]
    public function result(MA_Helper $helper)
    {
        $helper->doAuth();

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
     * Retrieves the deposited Pokemon
     */
    #[Route("/pokemonrse/worldexchange/get", name: "gts_get")]
    public function get_result(MA_Helper $helper)
    {
        $helper->doAuth();

        $result=get_deposited();

        if($result!=1){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * When a traded Pokemon has been retreived delete it from the server
     */
    #[Route("/pokemonrse/worldexchange/delete", name: "gts_get")]
    public function delete_result(MA_Helper $helper)
    {
        $helper->doAuth();

        $result=get_deposited();

        if($result!=1){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * When a deposited Pokemon has been retreived delete it from the server
     */
    #[Route("/pokemonrse/worldexchange/return", name: "gts_get")]
    public function return_result(MA_Helper $helper, GTS_Helper $GTS)
    {
        $helper->doAuth();

        $result=$GTS->remove_pokemon();

        if($result!=1){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * Deposited a Pokemon into the GTS
     */
    #[Route("/pokemonrse/worldexchange/post", name: "gts_get")]
    public function post_pokemon(MA_Helper $helper, PokemonGTSRepository $pokemon)
    {
        $helper->doAuth();

        $result=get_deposited();



        $pokemon = $pokemon->find($_GET['pid']);

        if($pokemon==NULL){
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }


        $pokemonData = $helper->decrypt_data();

        if($pokemon->getTrainerID!=substr($pokemonData,10,4)){
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }

        //Check Version (only emerald at the moment)
        if(substr($pokemonData,0,1) != 0x03){
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }
        //Check Language (only english at the moment)
        if(substr($pokemonData,7,1) != 0x02){
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }

        $pokemon->setVersion(substr($pokemonData,0,1));
        $pokemon->setRomHackID(substr($pokemonData,1,4));
        $pokemon->setRomHackVer(substr($pokemonData,5,2));
        $pokemon->setLanguage(substr($profileData,7,1));

        //Entity manager
        $em = $this->getDoctrine()->getManager();
        //Add to db
        $em->persist($pokemon);
        $em->flush();


        if($result!=1){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * Confirm Pokemon has been deposited during saving
     */
    #[Route("/pokemonrse/worldexchange/post_finish", name: "gts_get")]
    public function post_finish(MA_Helper $helper, GTS_Helper $GTS)
    {
        $helper->doAuth();

        $result=$GTS->remove_pokemon();

        if($result!=1){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * Search for Pokemon in the GTS
     */
    #[Route("/pokemonrse/worldexchange/search", name: "gts_get")]
    public function search_pokemon(MA_Helper $helper)
    {
        $helper->doAuth();

        $result=get_deposited();

        if($result!=1){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * Attempt to trade a Pokemon
     */
    #[Route("/pokemonrse/worldexchange/exchange", name: "gts_get")]
    public function exchange_pokemon(MA_Helper $helper)
    {
        $helper->doAuth();

        $result=get_deposited();

        if($result!=1){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * Confirm Pokemon has been traded during saving
     */
    #[Route("/pokemonrse/worldexchange/exchange_finish", name: "gts_get")]
    public function exchange_finish(MA_Helper $helper)
    {
        $helper->doAuth();

        $result=get_deposited();

        if($result!=1){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }
}
