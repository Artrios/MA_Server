<?php

namespace App\Service;

class GTS_Helper
{
    // Search for pokemon in the database
    public function query_pokemon(): string
    {
        searchPokemon($species, $minlevel, $maxlevel, $gender);
    }

    // Delete pokemon from the database
    public function remove_pokemon(): string
    {
        $pid = $_GET['pid'];
        $pokemon=findDepositedPokemon($pid);

        return $pokemon;
    }

    // Check if player has deposited pokemon
    public function check_result(): string
    {
        $pid = $_GET['pid'];
        dump("check0");
        $pokemon=findDepositedPokemon($pid);
        dump("check1");
        if($pokemon==NULL){
            dump("check2");
            return 0x0005;
        }
        else{
            if($pokemon->getIsExchanged()==0){
                dump("check3");
                return 0x0004;
            }
            else{
                dump("check4");
                return $pokemon->getPokemon;
            }
        }
    }

    // Retreive the deposited pokemon
    public function get_deposited(): stream_set_blocking
    {
        $pid = $_GET['pid'];
        $pokemon=findDepositedPokemon($pid);

        if($pokemon==NULL){
            return 0;
        }
        else{
            return $pokemon->getPokemon;
        }
    }
}