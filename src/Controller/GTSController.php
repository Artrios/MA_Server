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

        $result="";
        if($pokemon==NULL){
            dump("Pokemon not found");
            $result=0x0005;
        }
        else{
            dump("Pokemon Found");
            if($pokemon->getIsExchanged()==1){
                dump("Sending Pokemon");
                $result = $result.pack('V', $pokemon->getChecksum());
                $result = $result.pack('V', $pokemon->getPid());
                $result = $result.stream_get_contents($pokemon->getPokemon());
                $result = $result.pack('v', $pokemon->getDexId());
                $result = $result.pack('C', (int)stream_get_contents($pokemon->getGender()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon->getLevel()));
                $result = $result.pack('v', $pokemon->getRequestedDexId());
                $result = $result.pack('C', (int)stream_get_contents($pokemon->getRequestedGender()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon->getMinLevel()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon->getMaxLevel()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon->getTrainerGender()));
                $result = $result.pack('v', $pokemon->getTrainerId());
                $result = $result.pack('v', $pokemon->getSecretId());
                $result = $result.str_pad($pokemon->getOtname(), 7, "\xFF");
                $result = $result.pack('C', (int)stream_get_contents($pokemon->getCountry()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon->getRegion()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon->getTrainerClass()));
                $result = $result.pack('v', $pokemon->getIsExchanged());
                $result = $result.pack('v', $pokemon->getVersion());
                $result = $result.pack('v', $pokemon->getRomHackId());
                $result = $result.pack('v', $pokemon->getRomHackVer());
                $result = $result.pack('C', (int)stream_get_contents($pokemon->getLanguage()));
                $result = $result."\x00\x00\x00";
                dump(strlen($result));
                return new Response($result,Response::HTTP_OK,["content-length" => strlen($result)]);
            }
            else{
                dump("Pokemon not traded");
                $result=0x0004;
            }
        }

        dump(strlen($result));
        $ar = pack('n', $result);
        
        return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
    }

    /**
     * Retrieves the deposited Pokemon
     */
    #[Route("/pokemonrse/worldexchange/get", name: "gts_get")]
    public function get_result(MA_Helper $helper, EntityManagerInterface $entityManager)
    {
        $helper->doAuth();

        $repository = $entityManager->getRepository(PokemonGTS::class);

        $pid = $_GET['pid'];
        $pid = hexdec($pid);
        $pokemon=$repository->findDepositedPokemon($pid);

        if($pokemon==NULL){
            $result=0x0001;
        }
        else{
            $result=stream_get_contents($pokemon->getPokemon());
            $ar = pack('n', $result);
            return new Response($result,Response::HTTP_OK,["content-length" => 80]);
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
    }

    /**
     * When a traded Pokemon has been retreived delete it from the server
     */
    #[Route("/pokemonrse/worldexchange/delete", name: "gts_delete")]
    public function delete_result(MA_Helper $helper, GTS_Helper $GTS, EntityManagerInterface $entityManager)
    {
        dump("try Auth");
        $helper->doAuth();
        dump("gts_delete");
        $repository = $entityManager->getRepository(PokemonGTS::class);

        $pid = $_GET['pid'];
        $pid = hexdec($pid);
        $pokemon=$repository->findDepositedPokemon($pid);

        if($pokemon==NULL){
            dump("Pokemon not found");
            $result=0x0001;
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }
        
        dump("Deleting Pokemon");
        $entityManager->remove($pokemon);
        $entityManager->flush();
        $result=0x0001;

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
    }

    /**
     * When a deposited Pokemon has been retreived delete it from the server
     */
    #[Route("/pokemonrse/worldexchange/return", name: "gts_return")]
    public function return_result(MA_Helper $helper, GTS_Helper $GTS, EntityManagerInterface $entityManager)
    {
        $helper->doAuth();

        $repository = $entityManager->getRepository(PokemonGTS::class);

        $pid = $_GET['pid'];
        $pid = hexdec($pid);
        $pokemon=$repository->findDepositedPokemon($pid);

        if($pokemon==NULL){
            $result=0x0001;
        }
        else{
            $result=0x0001;
            $entityManager->remove($pokemon);
            $entityManager->flush();
        }

        $ar = pack('n', $result);
        return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
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
    #[Route("/pokemonrse/worldexchange/search", name: "gts_search")]
    public function search_pokemon(MA_Helper $helper, EntityManagerInterface $entityManager)
    {
        $helper->doAuth();
        dump("search");

        $searchData = $helper->decrypt_data();
        if($searchData == 0){
            dump("decrypt failed");
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }
        dump("decrypt successful");
        
        $checksum=unpack('V', substr($searchData,0,4))[1];
        $species=unpack('v', substr($searchData,4,2))[1];
        $gender="ANY";
        $minlevel=unpack('C', substr($searchData,7,1))[1];
        $maxlevel=unpack('C', substr($searchData,8,1))[1];

        $repository = $entityManager->getRepository(PokemonGTS::class);

        $pid = $_GET['pid'];
        $pid = hexdec($pid);
        $pokemon=$repository->searchPokemon($species, $minlevel, $maxlevel, $gender, $pid);
        dump("search complete");
        dump(count($pokemon));

        $result="";
        if($pokemon==NULL){
            $result=0x0001;
        }
        else{
            for($i=0;$i<count($pokemon);$i++){
                $result = $result.pack('V', $pokemon[$i]->getChecksum());
                $result = $result.pack('V', $pokemon[$i]->getPid());
                $result = $result.stream_get_contents($pokemon[$i]->getPokemon());
                $result = $result.pack('v', $pokemon[$i]->getDexId());
                $result = $result.pack('C', (int)stream_get_contents($pokemon[$i]->getGender()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon[$i]->getLevel()));
                $result = $result.pack('v', $pokemon[$i]->getRequestedDexId());
                $result = $result.pack('C', (int)stream_get_contents($pokemon[$i]->getRequestedGender()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon[$i]->getMinLevel()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon[$i]->getMaxLevel()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon[$i]->getTrainerGender()));
                $result = $result.pack('v', $pokemon[$i]->getTrainerId());
                $result = $result.pack('v', $pokemon[$i]->getSecretId());
                $result = $result.str_pad($pokemon[$i]->getOtname(), 7, "\xFF");
                $result = $result.pack('C', (int)stream_get_contents($pokemon[$i]->getCountry()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon[$i]->getRegion()));
                $result = $result.pack('C', (int)stream_get_contents($pokemon[$i]->getTrainerClass()));
                $result = $result.pack('v', $pokemon[$i]->getIsExchanged());
                $result = $result.pack('v', $pokemon[$i]->getVersion());
                $result = $result.pack('v', $pokemon[$i]->getRomHackId());
                $result = $result.pack('v', $pokemon[$i]->getRomHackVer());
                $result = $result.pack('C', (int)stream_get_contents($pokemon[$i]->getLanguage()));
                $result = $result."\x00\x00\x00";

            }
            dump(strlen($result));

        }

        //$ar = pack('n', $result);
        return new Response($result,Response::HTTP_OK,["content-length" => strlen($result)]);
    }

    /**
     * Attempt to trade a Pokemon
     */
    #[Route("/pokemonrse/worldexchange/exchange", name: "gts_exchange")]
    public function exchange_pokemon(MA_Helper $helper, EntityManagerInterface $entityManager)
    {
        dump("exchange");
        $helper->doAuth();

        dump("auth success");
        $pid = $_GET['pid'];
        $pid = hexdec($pid);

        $pokemonData = $helper->decrypt_data();
        if($pokemonData == 0){
            dump("decrypt failed");
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }

        dump("Sanity checks");
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
     * Confirm Pokemon has been traded during saving
     */
    #[Route("/pokemonrse/worldexchange/exchange_finish", name: "gts_exchange_finish")]
    public function exchange_finish(MA_Helper $helper, EntityManagerInterface $entityManager)
    {
        $helper->doAuth();

        $pid = $_GET['pid'];
        $pid = hexdec($pid);
        $pidold = $_GET['data'];
        $pidold = hexdec($pidold);

        dump($pid);
        dump($pidold);

        //Find old Pokemon
        $repository = $entityManager->getRepository(PokemonGTS::class);
        $pokemonold=$repository->findDepositedPokemon($pidold);

        if($pokemonold==NULL){
            dump("No old poke");
            $ar = pack('n', 0x0002);
            return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
        }

        if($pokemonold->getIsExchanged() != 0){
            dump("Old Pokemon not available");
            dump($pokemonold->getIsExchanged());
            $ar = pack('n', 0x0003);
            return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
        }

        //Edit old Pokemon
        $pokemonold->setIsExchanged(3);
        $entityManager->persist($pokemonold);
        $entityManager->flush();

        dump("Swell");
        dump($pid);
        dump($pokemonold->getPid());

        //Edit new Pokemon
        $repository = $entityManager->getRepository(PokemonGTS::class);
        $pokemon=$repository->findExchangedPokemon($pid);
        if($pokemon==NULL){
            $ar = pack('n', 0x0004);
            $pokemonold->setIsExchanged(0);
            $entityManager->persist($pokemonold);
            $entityManager->flush();
            return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
        }
        $pokemon->setPid($pidold);
        $pokemon->setIsExchanged(1);
        $entityManager->persist($pokemon);
        $entityManager->flush();

        $ar = pack('n', 0x0001);
        return new Response($ar,Response::HTTP_OK,["content-length" => 2]);
    }
}
