<?php
/**
 * Copyright 2015 Cristiano Longo
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *     
 * Convert the CSV file with the list of pharmacies released by the municipality of Catania into
 * an OWL file.
 *
 * The source csv file is read from standad input.
 *
 * @author Cristiano Longo
 * @license http://www.gnu.org/licenses/lgpl-3.0.html
 */
 
//constants indicating the meaning of csv columns
define('ADDRESS_POS','0');
define('NAME_POS','1');
define('ID_POS','2');
define('LON_POS','5');
define('LAT_POS','6');

/**
 * Generate an ID to be used for the individual related to a location.
 * It can be attacched to a base uri to create a location URI.
 */
function getIDByAddress($address){
	$address_no_accent=str_replace('à','a',str_replace('é','e',str_replace('ì','i',str_replace('ò','o',str_replace('ù','u',$address)))));
	return urlencode(strtolower(str_replace(' ','_',$address_no_accent)));
}

// At first print the ontology header
echo file_get_contents('pharmacies_preamble.owl.part');
echo "\t<!-- generated by csvCatania2PharmaciesOWL.php on ".date('d-m-Y-H-i')." -->\n";
 
 
//parse pharmacies in the csv provided on standard input
$template=file_get_contents('pharmacy_template.owl.part');

$in=fopen('php://stdin', 'r');

//skip the csv header
fgets($in);

//parse each row
$count=0;
while( $row = fgetcsv($in,1000,",") ){
	$address=$row[ADDRESS_POS];
	$name=$row[1];
	$id=$row[2];
	
	echo str_replace("?id", $row[ID_POS], 
		str_replace("?locationid", getIDbyAddress($address), 
		str_replace("?address", $address, 
		str_replace("?name", $row[NAME_POS], 
		str_replace("?lat", $row[LAT_POS],
		str_replace("?lon", $row[LON_POS], $template))))));
	$count++;
}

//then print footer
echo "\t <!-- $count pharmacies found -->\n";
echo "</rdf:RDF>\n"
?>
 