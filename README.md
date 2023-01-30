# ID mapping for genes automatically

## Automatic execution by crontab

```
# write in /etc/crontab
@monthly full/path/to/retrieve_human_uniprot_data.sh --your --parameter
```
## Output mapping results for each ID type

### UniprotID-based
- Uniprot ID --> Gene ID
- Uniprot ID --> Gene name
### GeneID-based
- Gene ID --> Uniprot ID
- Gene ID --> Gene name
### Genename-based
- Gene name --> Uniprot ID
- Gene name --> Gene ID

> 1. Only primary column mapping to others is meaningful.
> 2. Reviewed column is always corrosponding to Uniprot ID only.
> 3. Proteins are sorting by legnth descending which is helpful for unreviewed ones selections.

### updating log files for details
- date, time
- total number of proteins of the latest version
- added proteins comparing to the previous version
- removed proteins comparing to the previous version

## Validate the code and results
- extracted profile comparing to raw one



