<?php
	$uniID_file = "human_uniprot_ID-based_mapping.txt";
	$geneID_file = "human_gene_ID-based_mapping.txt";
	$genename_file = "human_gene_name-based_mapping.txt";
	$uni_data = rtrim(file_get_contents($uniID_file),"\n");
	$trim_uni_data = explode("\n",$uni_data);
	$arrGeneID = array();
	$arrGenename = array();
	echo "output geneID-based file...\noutput genename-based file...\n";
	foreach ($trim_uni_data as $numLine=>$line) {
		if ($numLine==0) { continue; }
		$listGeneID = array();
		$listGenename = array();
		$line = rtrim($line,"\t");
		$trimLine = explode("\t",$line);
		#(strpos($trimLine[1],";"))?(array_push($listGeneID,explode(";",$trimLine[1]))):(array_push($listGeneID,$trimLine[1]));
		$listGeneID = (
			(strpos($trimLine[1],";"))?(explode(";",$trimLine[1])):(array($trimLine[1])));
		$listGenename = (
			(strpos($trimLine[2],";"))?(explode(";",$trimLine[2])):(array($trimLine[2])));
		$listGeneID = array_diff($listGeneID,array("-"));
		$listGenename = array_diff($listGenename,array("-"));
		$arrGeneID_info = $trimLine;
		$arrGenename_info = $trimLine;
		unset($arrGeneID_info[1]);
		unset($arrGenename_info[2]);
		$arrGeneID_info = array_values($arrGeneID_info);
		$arrGenename_info = array_values($arrGenename_info);
		foreach ($listGeneID as $v){
			if (array_key_exists($v,$arrGeneID)) {
				for ($i=0;$i<count($arrGeneID[$v]);$i++) {
					$arrGeneID[$v][$i] .= ";".$arrGeneID_info[$i];
				}	
			}else {
				$arrGeneID[$v] = $arrGeneID_info; 
			}
		}
		foreach ($listGenename as $n) {
			if (array_key_exists($n,$arrGenename)) {
				for ($i=0;$i<count($arrGenename[$n]);$i++) {
					$arrGenename[$n][$i] .= ";".$arrGenename_info[$i];
				}			
			}else {
				$arrGenename[$n] = $arrGenename_info; 
			}
		
		}
	}

	$fp_geneID = fopen($geneID_file,"w");
	$geneID_colnames = array("GeneID","UniprotID","Gene_Name","Reviewed","Length");
	fwrite($fp_geneID, implode("\t",$geneID_colnames)."\n");
	foreach ($arrGeneID as $k=>$v) {
		fwrite($fp_geneID, $k."\t");
		fwrite($fp_geneID, implode("\t",$v)."\n");
	}
	fclose($fp_geneID);
	$fp_genename = fopen($genename_file,"w");
	$genename_colnames = array_replace($geneID_colnames,array(0=>"Gene_Name",2=>"GeneID"));
	fwrite($fp_genename, implode("\t",$genename_colnames)."\n");
	
	foreach ($arrGenename as $k=>$v) {
		fwrite($fp_genename, $k."\t");
		fwrite($fp_genename, implode("\t",$v)."\n");
	}
	fclose($fp_genename);
	
?>
