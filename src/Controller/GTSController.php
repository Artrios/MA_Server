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

        $repository = $entityManager->getRepository(PokemonGTS::class);

        $pid = $_GET['pid'];
        $pid = hexdec($pid);
        $pokemon=$repository->findDepositedPokemon($pid);

        if($pokemon==NULL){
            $result=0x0005;
        }
        else{
            if($pokemon->getIsExchanged()==1){
                $result=$pokemon->getPokemon();
            }
            else{
                $result=0x0004;
            }
        }

        dump(strlen($result));
        $ar = pack('n', $result);
        
        return new Response($ar,Response::HTTP_OK,["content-length" => strlen($result)]);
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
        if(hexdec(bin2hex(substr($pokemonData,113,2))) != 0x03){
            dump("version failed");
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }
        //Check Language (only english at the moment)
        if(hexdec(bin2hex(substr($pokemonData,120,1))) != 0x02){
            dump("lang failed");
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }

        $pokemon = new PokemonGTS();

        $pokemon->setChecksum(unpack('V', substr($pokemonData,0,4))[1]);
        $pokemon->setPid(unpack('V', substr($pokemonData,4,4))[1]);
        $pokemon->setPokemon(substr($pokemonData,8,80));
        $pokemon->setDexId(unpack('v', substr($pokemonData,88,2))[1]);
        $pokemon->setGender(unpack('C', substr($pokemonData,90,1))[1]);
        $pokemon->setLevel(unpack('C', substr($pokemonData,91,1))[1]);
        $pokemon->setRequestedDexId(unpack('v', substr($pokemonData,92,2))[1]);
        $pokemon->setRequestedGender(unpack('C', substr($pokemonData,94,1))[1]);
        $pokemon->setMinLevel(unpack('C', substr($pokemonData,95,1))[1]);
        $pokemon->setMaxLevel(unpack('C', substr($pokemonData,96,1))[1]);
        $pokemon->setTrainerGender(unpack('C', substr($pokemonData,97,1))[1]);
        $pokemon->setTrainerId(unpack('v', substr($pokemonData,98,2))[1]);
        $pokemon->setSecretId(unpack('v', substr($pokemonData,100,2))[1]);
        $pokemon->setOtname(substr($pokemonData,102,7));
        $pokemon->setCountry(unpack('C', substr($pokemonData,110,1))[1]);
        $pokemon->setRegion(unpack('C', substr($pokemonData,111,1))[1]);
        $pokemon->setTrainerClass(unpack('C', substr($pokemonData,112,1))[1]);
        $pokemon->setIsExchanged(2);
        $pokemon->setVersion(unpack('v', substr($pokemonData,114,2))[1]);
        $pokemon->setRomHackId(unpack('v', substr($pokemonData,116,2))[1]);
        $pokemon->setRomHackVer(unpack('v', substr($pokemonData,118,2))[1]);
        $pokemon->setLanguage(unpack('C', substr($pokemonData,120,1))[1]);

        //Add to db
        $entityManager->persist($pokemon);
        $entityManager->flush();


        $ar = pack('n', 0x0001);
        return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
    }

    /**
     * Confirm Pokemon has been deposited during saving
     */
    #[Route("/pokemonrse/worldexchange/post_finish", name: "gts_post_finish")]
    public function post_finish(MA_Helper $helper, EntityManagerInterface $entityManager)
    {
        $helper->doAuth();

        $repository = $entityManager->getRepository(PokemonGTS::class);

        $pid = $_GET['pid'];
        $pid = hexdec($pid);
        $pokemon=$repository->findDepositedPokemon($pid);

        if($pokemon==NULL){
            $result=0x0002;
        }
        else{
            $pokemon->setIsExchanged(0);
            $entityManager->persist($pokemon);
            $entityManager->flush();
            $result=0x0001;
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
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
