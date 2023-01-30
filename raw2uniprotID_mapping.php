<?php
	function get_uniprot_update_details(){
		$old_proteins = array();	
		$old_content = rtrim(file_get_contents("human_uniprot_ID-based_mapping.txt"),"\n");
		$arr_old_uni = explode("\n",$old_content);
		foreach ($arr_old_uni as $k=>$l) {
			if ($k==0) { continue; }
			$triml = explode("\t",$l);
			array_push($old_proteins,$triml[0]);
		}
		return $old_proteins;
	}

	echo "output uniprotID-based file...\n";
	// refresh and compare downloaded proteins to the previous one
	global $arrOldProteins;
	$fp_log = fopen("log/uniprot_update_".date("Ymd").".log","w");
	$fulltime = date("Y/m/d H:i:s");
	fwrite($fp_log,"Time: ".$fulltime."\n");
	if (file_exists("human_uniprot_ID-based_mapping.txt")) {
		$arrOldProteins = get_uniprot_update_details();
	}else{
		$arrOldProteins = array();
	}
	
	$raw_file = "HUMAN_9606_idmapping.dat";
	$detail_file = "Uniprot_HUMAN_information.tsv";
	$data = rtrim(file_get_contents($raw_file),"\n");
	$trimdata = explode("\n",$data);
	$detail = rtrim(file_get_contents($detail_file),"\n");
	$trimdetail = explode("\n",$detail);
	// reviewed/unreviewed and length info
	$arrStatus = array();
	foreach ($trimdetail as $k=>$line) {
		if ($k==0) { continue; }
		$trimline = explode("\t",$line);
		$arrStatus[$trimline[0]] = array("Length"=>$trimline[5], "Reviewed"=>$trimline[6]);
	}
	// ID mapping
	$arrID = array();
	foreach ($trimdata as $k=>$line) {
		$trimline = explode("\t",$line);
		$uniID = $trimline[0];	
		$title = (($trimline[1]=='Gene_Synonym')?'Gene_Name':$trimline[1]);
		$value = $trimline[2];
		if (in_array($title,array('Gene_Name', 'GeneID'))) {
			if (array_key_exists($uniID,$arrID)){
				if (array_key_exists($title,$arrID[$uniID])) {	
					$arrID[$uniID][$title] .= ";".$value;
				}else{	
					$arrID[$uniID][$title] = $value;
				}				
			}else{
				$arrID[$uniID] = array($title=>$value);
			}
		}
	}
	$arrFinal = array_merge_recursive($arrID,$arrStatus);
	// sort proteins by length
	$arrLength = array();
	foreach ($arrFinal as $k=>$v){
		$arrLength[$k] = $v["Length"];
	}
	array_multisort($arrLength, SORT_DESC, $arrFinal);
	// output uniprotID-based files and one log file
	$fp_uniID = fopen("human_uniprot_ID-based_mapping.txt","w");
	$times = 0;
	$uni_colnames = array("UniprotID","GeneID","Gene_Name","Reviewed","Length"); 
	foreach ($arrFinal as $uni=>$arrInfo){
		if ($times==0) { fwrite($fp_uniID,implode("\t",$uni_colnames)."\n"); }
		$content = array($uni);
		for ($i=1;$i<count($uni_colnames);$i++) {
			$v = (array_key_exists($uni_colnames[$i],$arrInfo)?$arrInfo[$uni_colnames[$i]]:"-");
			array_push($content,$v);
		}
		fwrite($fp_uniID,implode("\t",$content)."\n");
		$times++;
	}
	fclose($fp_uniID);
	
	$arrNewProteins = get_uniprot_update_details();
	$numNewProteins = count($arrNewProteins);
	fwrite($fp_log,"The number of proteins: {$numNewProteins}\n");
	$arrAddProteins = (
		empty($arrOldProteins)?($arrNewProteins):(array_diff($arrNewProteins,$arrOldProteins)));
	$arrDelProteins = array_diff($arrOldProteins,$arrNewProteins);
	(empty($arrAddProteins))?(fwrite($fp_log,"Added proteins: \nNone\n")):(fwrite($fp_log,"Added proteins:\n".implode(";",$arrAddProteins)."\n"));
	(empty($arrDelProteins))?(fwrite($fp_log,"Removed proteins: \nNone\n")):(fwrite($fp_log,"Removed proteins:\n".implode(";",$arrDelProteins)."\n"));
	fclose($fp_log);
?>
