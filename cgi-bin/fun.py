#! /usr/bin/python
def getAminoAcid(codon = "ATG"):
    codonUsage ={"A":["GCG","GCA","GCT","GCC"]
                ,"C":["TGT","TGC"]
                ,"D":["GAT","GAC"]
                ,"E":["GAG","GAA"]
                ,"F":["TTT","TTC"]
                ,"G":["GGG","GGA","GGT","GGC"]
                ,"H":["CAT","CAC"]
                ,"I":["ATA","ATT","ATC"]
                ,"K":["AAG","AAA"]
                ,"L":["TTG","TTA","CTG","CTA","CTT", "CTC"]
                ,"M":["ATG"]
                ,"N":["AAT","AAC"]
                ,"P":["CCG","CCA","CCT","CCC"]
                ,"Q":["CAG","CAA"]
                ,"R":["AGG","AGA","CGG","CGA","CGT","CGC"]
                ,"S":["AGT","AGC","TCG","TCA","TCT","TCC"]
                ,"T":["ACG","ACA","ACT","ACC"]
                ,"V":["GTG","GTA","GTT","GTC"]
                ,"W":["TGG"]
                ,"Y":["TAT","TAC"]
                ,"*":["TGA","TAG","TAA"]}
    for amino in codonUsage:
        if(codon in codonUsage[amino]):
            return amino


def getBestCodon(amino,cub):
    return cub[amino].keys()[cub[amino].values().index(max(cub[amino].values()))]


def parseCodonUsage(codonUsage):
    import re
    r = re.compile("([A-Z]{3})\s([A-Z,'*'])\s([-+]?[0-9]*\.?[0-9]*)")
    matches = re.findall(r, codonUsage)
    usageTable = {}
    if(len(matches) == 64):
        for match in matches:
            if (match[1] in usageTable):
                usageTable[match[1]][match[0]] = match[2]
            else:
                usageTable[match[1]] = {match[0]: match[2]}
        return {'name':'usageTable','status':1,'payload':usageTable}
    else:
        return {'status':0,'error':'Error parsing the table'}


def sliceByFasta(fasta):
    for x in range(len(fasta)):
        slice = fasta[x:x + 60]
        if ((sum([ord(i) for i in set(slice)]) / 4) == 71):
            return [fasta[0:x], fasta[x:]]