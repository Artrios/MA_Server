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

    public const RSEtoASCIITable = [
        ' ',
        0x86, 0x87, 0x88, 0x89, 0x8a, 0x8b, 0x8c, 0x8d,
        0x8e, 0x8f, 0x90, 0x91, 0x92, 0x93, 0x94, 0x95,
        0x96, 0x97, 0x98, 0x99, 0x9a, 0x9b, 0x9c, 0x9d,
        0x9e, 0x9f, 0xa0, 0xe0, 0xe1, 0xe2, 0xe3, 0xe4,
        0xe5, 0xe6, 0xe7, 0xe8, 0xe9, 0xea, 0xeb, 0xec,
        0xed, 0xee, 0xef, 0xf0, 0x7b, 0xf1, 0x7c, 0x7d,
        0x7e, 0x7f, 0x80, 0x81, 0x82, 0x83, 0x07, 0x08,
        0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f, 0x10,
        0x11, 0x12, 0x13, 0x14, 0x15, 0x16, 0x17, 0x18,
        0x19, 0x1a, 0x1b, 0x1c, 0x1d, 0x1e, 0x1f, 0x84,
        0xb1, 0xb2, 0xb3, 0xb4, 0xb5, 0xb6, 0xb7, 0xb8,
        0xb9, 0xba, 0xbb, 0xbc, 0xbd, 0xbe, 0xbf, 0xc0,
        0xc1, 0xc2, 0xc3, 0xc4, 0xc5, 0xc6, 0xc7, 0xc8,
        0xc9, 0xca, 0xcb, 0xcc, 0xcd, 0xce, 0xcf, 0xd0,
        0xd1, 0xd2, 0xd3, 0xd4, 0xd5, 0xd6, 0xd7, 0xd8,
        0xd9, 0xda, 0xdb, 0xdc, 0xa6, 0xdd, 0xa7, 0xa8,
        0xa9, 0xaa, 0xab, 0xac, 0xad, 0xae, 0xf2, 0xf3,
        0xf4, 0xf5, 0xf6, 0xf7, 0xf8, 0xf9, 0xfa, 0xfb,
        0xfc, 0xfd, 0xfe, 0xff, 0x01, 0x02, 0x03, 0x04,
        0x05, 0x06, 0x3b, 0x3c, 0x3d, 0x3e, 0x3f, 0xaf,
        '0',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '!',
        0xdf, 0xa1, 0xb0, 0xa5, 0xde, 0x24, 0x2a,
        0xa2, 0xa3, 0x22, 0x23, 0x20, 0xa4, 0x20,
        '/',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
        0x20, 0x20, 0x2b, 0x5b, 0x5c, 0x5d, 0x5e, 0x5f,
        ' ',
        ' ',
        ' ',
        ' ',
        ' ',
        ' ',
        ' ',
        ' ',
        0
    ];

    //Convert a Pokemon string to ASCII
    public function PkmnStrToASCII(string $pkmnStr)
    {
        $asciiStr = $pkmnStr;
        
        for ($i = 0; $i < strlen($pkmnStr); $i++){
            if(ord($pkmnStr[$i]) == 255){
                $asciiStr = substr($asciiStr,0,$i);
                break;
            } else{
                $asciiStr[$i] = self::RSEtoASCIITable[ord($pkmnStr[$i])];
            }
        }
        
        return $asciiStr;
    }

    public function SubstructCase(int $substructType, $v1, $v2, $v3, $v4)
    {

        switch ($substructType)
        {      
        case 0: 
            return $v1;
        case 1:  
            return $v2;
        case 2:  
            return $v3;
        case 3: 
            return $v4;
        }
    }

    public function GetSubstruct(int $personality, int $substructType)
    {
 
        switch ($personality % 24)
        {
            case 0: 
                return $this->SubstructCase($substructType,0,1,2,3);
            case 1: 
                return $this->SubstructCase($substructType,0,1,3,2);
            case 2: 
                return $this->SubstructCase($substructType,0,2,1,3);
            case 3: 
                return $this->SubstructCase($substructType,0,3,1,2);
            case 4: 
                return $this->SubstructCase($substructType,0,2,3,1);
            case 5: 
                return $this->SubstructCase($substructType,0,3,2,1);
            case 6: 
                return $this->SubstructCase($substructType,1,0,2,3);
            case 7: 
                return $this->SubstructCase($substructType,1,0,3,2);
            case 8: 
                return $this->SubstructCase($substructType,2,0,1,3);
            case 9: 
                return $this->SubstructCase($substructType,3,0,1,2);
            case 10: 
                return $this->SubstructCase($substructType,2,0,3,1);
            case 11: 
                return $this->SubstructCase($substructType,3,0,2,1);
            case 12: 
                return $this->SubstructCase($substructType,1,2,0,3);
            case 13: 
                return $this->SubstructCase($substructType,1,3,0,2);
            case 14: 
                return $this->SubstructCase($substructType,2,1,0,3);
            case 15: 
                return $this->SubstructCase($substructType,3,1,0,2);
            case 16: 
                return $this->SubstructCase($substructType,2,3,0,1);
            case 17: 
                return $this->SubstructCase($substructType,3,2,0,1);
            case 18: 
                return $this->SubstructCase($substructType,1,2,3,0);
            case 19: 
                return $this->SubstructCase($substructType,1,3,2,0);
            case 20: 
                return $this->SubstructCase($substructType,2,1,3,0);
            case 21: 
                return $this->SubstructCase($substructType,3,1,2,0);
            case 22: 
                return $this->SubstructCase($substructType,2,3,1,0);
            case 23: 
                return $this->SubstructCase($substructType,3,2,1,0);
        }

        return substruct;
    }
}