<?php

namespace App\Controller;

use App\Entity\PokemonGTS;
use App\Service\MA_Helper;
use App\Service\GTS_Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
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

        return new Response($ar,Response::HTTP_OK);
    }

    /**
     * Checks if the players deposited pokemon in the GTS has been traded or not
     * 0x0004 = The Pokemon has not been traded
     * 0x0005 = The player has no deposited pokemon
     * pkmn = If the Pokemon has been traded it returns the new Pokemon (100 bytes)
     */
    #[Route("/pokemonrse/worldexchange/result", name: "gts_result")]
    public function result(MA_Helper $helper, EntityManagerInterface $entityManager)
    {
        $helper->doAuth();
        dump("success!");

        $repository = $entityManager->getRepository(PokemonGTS::class);

        $pid = $_GET['pid'];
        dump("check0");
        $pokemon=$repository->findDepositedPokemon($pid);
        dump("check1");

        if($pokemon==NULL){
            dump("check2");
            $result=0x0005;
        }
        else{
            if($pokemon->getIsExchanged()==0){
                dump("check3");
                $result=0x0004;
            }
            else{
                dump("check4");
                $result=$pokemon->getPokemon();
            }
        }

        //$result=$GTS->check_result();
        dump(strlen($result));
        $ar = pack('n', $result);
        
        return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
    }

    /**
     * Retrieves the deposited Pokemon
     */
    #[Route("/pokemonrse/worldexchange/get", name: "gts_get")]
    public function get_result(MA_Helper $helper, GTS_Helper $GTS)
    {
        $helper->doAuth();

        $result=$GTS->get_deposited();

        if(strlen($result)!=100){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * When a traded Pokemon has been retreived delete it from the server
     */
    #[Route("/pokemonrse/worldexchange/delete", name: "gts_delete")]
    public function delete_result(MA_Helper $helper, GTS_Helper $GTS, EntityManagerInterface $entityManager)
    {
        $helper->doAuth();

        $result=$GTS->remove_pokemon();

        if($result==NULL){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $entityManager->remove($result);
        $entityManager->flush();

        $ar = pack('n', 0x0001);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * When a deposited Pokemon has been retreived delete it from the server
     */
    #[Route("/pokemonrse/worldexchange/return", name: "gts_return")]
    public function return_result(MA_Helper $helper, GTS_Helper $GTS, EntityManagerInterface $entityManager)
    {
        $helper->doAuth();

        $result=$GTS->remove_pokemon();

        if($result==NULL){
            return new Response($ar,Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $entityManager->remove($result);
        $entityManager->flush();

        $ar = pack('n', 0x0001);
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * Deposited a Pokemon into the GTS
     */
    #[Route("/pokemonrse/worldexchange/post", name: "gts_post")]
    public function post_pokemon(MA_Helper $helper, EntityManagerInterface $entityManager)
    {
        $helper->doAuth();

        $pokemonData = $helper->decrypt_data();
        if($pokemonData == 0){
            dump("decrypt failed");
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }
        

        //Check Version (only emerald at the moment)

        dump("post");
        if(substr($pokemonData,113,2) != 0x03){
            dump("version failed");
            dump(ord(substr($pokemonData,110,1)));//0
            dump(ord(substr($pokemonData,111,1)));//0
            dump(ord(substr($pokemonData,112,1)));//0
            dump(ord(substr($pokemonData,113,3)));//0
            dump(ord(substr($pokemonData,114,1)));//3
            dump(ord(substr($pokemonData,115,1)));//0
            dump(ord(substr($pokemonData,116,1)));//13
            dump(ord(substr($pokemonData,117,1)));
            dump(ord(substr($pokemonData,118,1)));
            dump(ord(substr($pokemonData,119,1)));
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }
        //Check Language (only english at the moment)
        if(substr($pokemonData,146,1) != 0x02){
            dump("lang failed");
            dump(ord(substr($pokemonData,119,2)));
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }

        $pokemon = new PokemonGTS();

        $pokemon->setChecksum(hexdec(substr($pokemonData,0,4)));
        $pokemon->setPid(hexdec(substr($pokemonData,4,4)));
        $pokemon->setPokemon(hex2bin(substr($pokemonData,8,80)));
        $pokemon->setDexId(hexdec(substr($pokemonData,88,2)));
        $pokemon->setGender(hex2bin(substr($pokemonData,90,1)));
        $pokemon->setLevel(hex2bin(substr($pokemonData,91,1)));
        $pokemon->setRequestedDexId(hexdec(substr($pokemonData,92,2)));
        $pokemon->setRequestedGender(hex2bin(substr($pokemonData,94,1)));
        $pokemon->setMinLevel(hex2bin(substr($pokemonData,95,1)));
        $pokemon->setMaxLevel(hex2bin(substr($pokemonData,96,1)));
        $pokemon->setTrainerGender(hex2bin(substr($pokemonData,97,1)));
        $pokemon->setTrainerId(hexdec(substr($pokemonData,98,2)));
        $pokemon->setSecretId(hexdec(substr($pokemonData,100,2)));
        $pokemon->setOtname(substr($pokemonData,102,7));
        $pokemon->setCountry(hex2bin(substr($pokemonData,109,1)));
        $pokemon->setRegion(hex2bin(substr($pokemonData,110,1)));
        $pokemon->setTrainerClass(hex2bin(substr($pokemonData,111,1)));
        $pokemon->setIsExchanged(hexdec(substr($pokemonData,112,2)));
        $pokemon->setVersion(hexdec(substr($pokemonData,113,2)));
        $pokemon->setRomHackId(hexdec(substr($pokemonData,115,2)));
        $pokemon->setRomHackVer(hexdec(substr($pokemonData,117,2)));
        $pokemon->setLanguage(hex2bin(substr($pokemonData,119,1)));

        //Entity manager
        $em = $this->getDoctrine()->getManager();
        //Add to db
        $em->persist($pokemon);
        $em->flush();


        $ar = pack('n', 0x0001);
        return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
    }

    /**
     * Confirm Pokemon has been deposited during saving
     */
    #[Route("/pokemonrse/worldexchange/post_finish", name: "gts_get")]
    public function post_finish(MA_Helper $helper, GTS_Helper $GTS)
    {
        $helper->doAuth();

        $result=0x0001;

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
    public function search_pokemon(MA_Helper $helper, EntityManagerInterface $entityManager)
    {
        $helper->doAuth();

        $repository = $entityManager->getRepository(PokemonGTS::class);
        $pokemon=$repository->searchPokemon($species, $minlevel, $maxlevel, $gender);

        if($result==NULL){
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
