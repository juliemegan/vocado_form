<?php

// The function to execute in this script.
// If the func variable sent by the ajax call is not empty, set this func variable to it.
// Otherwise, set the func variable to null.
//$func = !empty($_POST['func']) ? $_POST['func'] : null;
$func = !empty($_POST['func']) ? $_POST['func'] : null;

$doc_code = !empty($_POST['doc_code']) ? $_POST['doc_code'] : null;
$info_array = !empty($_POST['info_array']) ? $_POST['info_array'] : null;

function get_all_doc_names_codes() {
	$names_codes = array(
		"FA-1040X" => "1040X Tax Form",
		"FA-1099G" => "1099G Tax Form",
		"FA-ActiveConfirmLetter" =>	"Active Confirmation Letter",
		"FA-AddFinSupport-Dep" => "Additional Financial Support Form - Dependent",
		"FA-AddFinSupport-Ind" => "Additional Financial Support Form - Independent",
		"FA-AssetInfo" => "Asset Information Form",
		"TEST" => "TEST"
	);
	
	echo json_encode($names_codes);
}

// Given a Document Code, returns the keys and value associated with it. 
// DEBUG - currently this function does not parse each row accurately.
// e.g. For 04_b_DOCMETADATA.csv, there should be 17 columns. However, the
// current parsing method will sometimes returns > 17 due to column values containing the
// pattern ",". TBD How to resolve this.
function get_all_keys_specs_by_doc_code($doc_code) {
	// Gets the contents of the file and puts it into a variable as a String.
	$csv_str = file_get_contents("04_b_DOCMETADATA.csv");
	
	// Parses the String by line breaks and puts it into a variable as an Array.
	$csv_rows = preg_split("/[\n\r]+/", $csv_str);
	
	// Init a new array for the Associative Array that will be returned by this function.
	$csv_data = array();
	
	// Iterate through each row of the CSV data.
	foreach($csv_rows as $csv_row) {
		// Parses the row by the pattern "," to get each column's value and puts it
		// into an Array. TODO: debug this- see note in function signature.
		$csv_items = preg_split("/\",\"/", $csv_row);
		// Trims the leading double-quote in the first column (left over from parsing earlier).
		$csv_items[0] = trim($csv_items[0], "\"");
		// Gets the size of the array and subtracts 1 to get the last index of the array.
		$last_index = sizeof($csv_items) - 1;
		// Trims the trailing double-quote in the last column.
		$csv_items[$last_index] = trim($csv_items[$last_index], "\"");
		// Pushes the row values as an array to the csv_data associative array that 
		// will be returned by this function.
		array_push($csv_data, $csv_items);
	}
	// Gets the first row (column headers) and parses it by commas, and puts it
	// into an array.
	$csv_header_row_items = preg_split("/[,]+/", $csv_data[0][0]);
	
	// For Testing
	//echo "header row items size: " . sizeof($csv_header_row_items) . "<br>";
	//print_r($csv_header_row_items) . "<br>";
	
	// Init the Associtative rray that will be returned by this function.
	$csv_assoc_array = array();
	
	// Iterate through each row in the csv (except the header row).
	for ($i = 1; $i < sizeof($csv_data); $i++) {
		$csv_row_items = $csv_data[$i];
		
		// Only return rows where the document code is equal to the document 
		// code we are interested in.
		if ($csv_row_items[0] != $doc_code) {
			continue 1;
		}
		
		// For Testing
		//echo "csv_row_items size: " . sizeof($csv_row_items) . "<br>";
		//if (sizeof($csv_row_items) > 17) {
		//	print_r($csv_row_items) . "<br>";
		//}
		for ($j = 0; $j < sizeof($csv_header_row_items); $j++) {
			$key = $csv_header_row_items[$j];
			$value = isset($csv_row_items[$j]) ? $csv_row_items[$j] : null;
			$csv_assoc_array[$i - 1][$key] = $value;
			
			// For Testing
			//echo "<br>key: " . $key . "<br>value: " . $value;
		}
	}
	// For Testing
	//print_r($csv_assoc_array);
	echo json_encode($csv_assoc_array);
}




// function definition to convert array to xml
// http://www.codexworld.com/convert-array-to-xml-in-php/
function array_to_xml($array, &$xml_info) {
	foreach($array as $key => $value) {
		if(is_array($value)) {
			if(!is_numeric($key)){
				$subnode = $xml_info->addChild("$key");
				array_to_xml($value, $subnode);
			}else{
				//$subnode = $xml_user_info->addChild("item$key");
				$subnode = $xml_info->addChild("MetadataField");
				array_to_xml($value, $subnode);
			}
		}else {
			$xml_info->addChild("$key",htmlspecialchars("$value"));
		}
	}
}

function create_xml_file($info_array) {
	$info_array = json_decode($info_array);
	
	//creating object of SimpleXMLElement
	$xml_user_info = new SimpleXMLElement("<?xml version=\"1.0\"?><user_info></user_info>");
	$info = new SimpleXMLElement(
			"<?xml version=\"1.0\" encoding=\"utf-8\"?><FasDocumentReceiptEvent></FasDocumentReceiptEvent>");
	
	//function call to convert array to xml
	//array_to_xml($users_array,$xml_user_info);
	array_to_xml($info_array,$info);
	$info->addAttribute('xmlns:xmlnsaa', "http://www.vocado.com/vm/fas");
	$info->addAttribute('xmlns:xmlnsvc', "http://www.vocado.com/vm/common");
	
	//saving generated xml file
	//$xml_file = $xml_user_info->asXML('users.xml');
	$xml_file = $info->asXML('info.xml');
	
	//success and error message based on xml creation
	if($xml_file){
		echo 'XML file has been generated successfully.';
	}else{
		echo 'XML file generation error.';
	}
}






switch($func) {
	case "get_all_doc_names_codes":
		get_all_doc_names_codes();
		break;
	case "get_all_keys_specs_by_doc_code":
		get_all_keys_specs_by_doc_code($doc_code);
		break;
	case "create_xml_file":
		create_xml_file($info_array);
		break;
	default: 
		$func_call_error = array(
			"error"	=> "An invalid function name was called: " . $func
		);
		echo json_encode($func_call_error);
		break;
}
?>