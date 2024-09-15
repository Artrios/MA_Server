<?php

namespace App\Controller;

use App\Entity\UserProfiles;
use App\Service\MA_Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/pokemonrse/common", name="app_profile")
     */
    #[Route("/pokemonrse/common", name: "app_profile")]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }


    /**
     * @Route("/pokemonrse/common/createprofile", name="create_profile")
     */
    #[Route("/pokemonrse/common/createprofile", name: "create_profile")]
    public function create(MA_Helper $helper, EntityManagerInterface $entityManager)
    {
        $pid = $_GET['pid'];
        $helper->doAuth();

        //create new user profile
        $profile = new UserProfiles();

        $profile->setVersion(0);
        $profile->setRomHackID(0);
        $profile->setRomHackVer(0);
        $profile->setLanguage(0);
        $profile->setCountry(0);
        $profile->setRegion(0);
        $profile->setTrainerID(hexdec($pid));
        $profile->setTrainerName('0');
        $profile->setMAC('0');
        $profile->setEmail('0');
        $profile->setNotify(0);
        $profile->setClientSecret(0);
        $profile->setMailSecret(0);

        //Entity manager
        //$em = $this->getDoctrine()->getManager();
        //Add to db
        $entityManager->persist($profile);
        $entityManager->flush();

        $ar = pack('N', $profile->getId());

        return new Response($ar,Response::HTTP_OK,["content-length" => 4]);;
    }

    /**
     * @Route("/pokemonrse/common/setprofile", name="set_profile")
     */
    #[Route("/pokemonrse/common/setprofile", name: "set_profile")]
    public function set(MA_Helper $helper, UserProfilesRepository $profile)
    {
        $helper->doAuth();

        $profile = $profile->find($_GET['pid']);

        if($profile==NULL){
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }


        $profileData = $helper->decrypt_data();

        if($profile->getTrainerID!=substr($profileData,10,4)){
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }

        //Check Version (only emerald at the moment)
        if(substr($profileData,0,1) != 0x03){
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }
        //Check Language (only english at the moment)
        if(substr($profileData,7,1) != 0x02){
            return new Response('',Response::HTTP_UNAUTHORIZED);
        }

        $profile->setVersion(substr($profileData,0,1));
        $profile->setRomHackID(substr($profileData,1,4));
        $profile->setRomHackVer(substr($profileData,5,2));
        $profile->setLanguage(substr($profileData,7,1));
        $profile->setCountry(substr($profileData,8,1));
        $profile->setRegion(substr($profileData,9,1));
        $profile->setTrainerID(substr($profileData,10,4));
        $profile->setTrainerName(substr($profileData,14,16));
        $profile->setMAC(substr($profileData,30,6));
        //$profile->setEmail(substr($profileData,36,56)); Not for the game to set
        //$profile->setNotify(substr($profileData,92,4)); Not for the game to set
        $profile->setClientSecret(substr($profileData,96,2));
        $profile->setMailSecret(substr($profileData,98,2));

        //Entity manager
        $em = $this->getDoctrine()->getManager();
        //Add to db
        $em->persist($profile);
        $em->flush();

        //Everything is good
        $codeA = 0x00000000;
        $codeB = 0x00000000;

        $ar = pack('J', $codeA.$codeB);

        return new Response($ar,Response::HTTP_OK,["content-length" => 8]);
    }
    
}
