<?php
	$uniID_file = "uniprot_ID-based_mapping.txt";
	$geneID_file = "gene_ID-based_mapping.txt";
	$genename_file = "gene_name-based_mapping.txt";
	$rawUni = rtrim(file_get_contents($uniID_file),"\n");
	$trimUni = explode("\n",$rawUni);
	$rawGeneID = "";
	$rawGenename = "";
	foreach ($trimUni as $line) {
		$trimline = explode("\t",$line);
		$rawGeneID .= $trimline[1].";"; 
		$rawGenename .= $trimline[2].";"; 
	}	
	$arrRawGeneID = array_unique(explode(";",$rawGeneID));
	$arrRawGeneID = array_diff($arrRawGeneID,array("-",""));
	$geneID = rtrim(file_get_contents($geneID_file),"\n");
	$trimGeneID = explode("\n",$geneID);
	$arrRawGenename = array_unique(explode(";",$rawGenename));
	$arrRawGenename = array_diff($arrRawGenename,array("-",""));
	$genename = rtrim(file_get_contents($genename_file),"\n");
	$trimGenename = explode("\n",$genename);
	echo "ID\tRaw unique proteins\tExtracted unique proteins\n";
	echo "GeneID\t".(string)(count($arrRawGeneID)-1)."\t".(string)(count($trimGeneID)-1)."\n";
	echo "Genename\t".(string)(count($arrRawGenename)-1)."\t".(string)(count($trimGenename)-1)."\n";
?>
