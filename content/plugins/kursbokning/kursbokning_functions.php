<?php

// funktion för att finna om ett fält ska vara med, och i så fall om det är obligatoriskt eller inte
// returnerar 0 för exkludera fält, 1 för inkludera fält ej obl, 2 för inkludera fält obl
function get_field_setting($find, $fields) {
	$q = 0;
	if(is_array($fields)) {
		foreach($fields as $field) {
			if($field){
				if(strrpos($field, $find)!== false) {
					$q = 1 + substr($field, -1);
					break;
				}
			}
		}
		return $q;
	}
}

$swelan = array(
	"10" => "Blekinge län",
	"20" => "Dalarnas län",
	"09" => "Gotlands län",
	"21" => "Gävleborgs län",
	"13" => "Hallands län",
	"23" => "Jämtlands län",
	"06" => "Jönköpings län",
	"08" => "Kalmar län",
	"07" => "Kronobergs län",
	"25" => "Norrbottens län",
	"12" => "Skåne län",
	"01" => "Stockholms län",
	"04" => "Södermanlands län",
	"03" => "Uppsala län",
	"17" => "Värmlands län",
	"24" => "Västerbottens län",
	"22" => "Västernorrlands län",
	"19" => "Västmanlands län",
	"14" => "Västra Götalands län",
	"18" => "Örebro län",
	"05" => "Östergötlands län"
);

$swelan_char_name = array(
	"K" => "Blekinge län",
	"W" => "Dalarnas län",
	"I" => "Gotlands län",
	"X" => "Gävleborgs län",
	"N" => "Hallands län",
	"Z" => "Jämtlands län",
	"F" => "Jönköpings län",
	"H" => "Kalmar län",
	"G" => "Kronobergs län",
	"BD" => "Norrbottens län",
	"M" => "Skåne län",
	"AB" => "Stockholms län",
	"D" => "Södermanlands län",
	"C" => "Uppsala län",
	"S" => " Värmlands län",
	"AC" => "Västerbottens län",
	"Y" => "Västernorrlands län",
	"U" => "Västmanlands län",
	"O" => "Västra Götalands län",
	"T" => "Örebro län",
	"E" => "Östergötlands län"
);


?>
