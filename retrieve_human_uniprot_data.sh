#!/bin/bash

#################################################################################################
# add following command in /etc/crontab to enable automatic execution:				            #
# @monthly full/path/to/retrieve_human_uniprot_data.sh  						                #
#################################################################################################

# download Uniprot human data 
wget "https://ftp.uniprot.org/pub/databases/uniprot/current_release/knowledgebase/idmapping/by_organism/HUMAN_9606_idmapping.dat.gz"
gunzip "HUMAN_9606_idmapping.dat.gz"
curl -H "Accept: text/plain; format=tsv" "https://rest.uniprot.org/uniprotkb/stream?compressed=true&fields=accession%2Cid%2Cprotein_name%2Cgene_names%2Corganism_name%2Clength%2Creviewed&format=tsv&query=%28%2A%29%20AND%20%28model_organism%3A9606%29" > "Uniprot_HUMAN_information.tsv.gz"
gunzip "Uniprot_HUMAN_information.tsv.gz"

# split raw data into uniprotID-based table and document log file
php raw2uniprotID_mapping.php 
# convert to genename and geneID-based table
php uniprotID2others_mapping.php
