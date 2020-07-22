#! /usr/bin/python
import fun
print "Content-type: application/json\n\n"

import cgi,json
import cgitb; cgitb.enable()

form = cgi.FieldStorage()
seq = fun.sliceByFasta(str(form.getvalue('seq')))
fastaHeader,seq = ("[OPTIMIZED] " + seq[0]),seq[1].replace(" ",'')
seq = seq.replace("\n","")
seq = seq.replace('\r','')
cub = str(form.getvalue('cub'))


parsedCub = fun.parseCodonUsage(cub)
status = parsedCub["status"]
if(status == 1 and (len(seq) % 3 == 0)):
    parsedCub = parsedCub['payload']
    aminos = [fun.getAminoAcid(seq[i:i+3]) for i in range(0,len(seq),3)]
    proteinSeq = "".join(aminos)

    optimized = []
    for amino in aminos:
        optimized.append(fun.getBestCodon(amino,parsedCub))

    optimized = "".join(optimized).replace('U', 'T')
    fastaOptimized = "".join(optimized) #+re.sub("(.{60})", "\\1\n", optimized, 0, re.DOTALL)
    #fastaOptimized = re.sub("(.{60})", "\\1\n", optimized, 0, re.DOTALL)

    print json.dumps({"status":status,'cub':parsedCub,'fastaHeader':fastaHeader,'seq':fastaOptimized,'aminos':proteinSeq,'orseq':seq})
    #print json.JSONEncoder().encode({"status":status,'cub':parsedCub,'fastaHeader':fastaHeader,'seq':fastaOptimized,'aminos':proteinSeq,'orseq':seq})

elif((len(seq) % 3 != 0)):
    print json.dumps({'status':0,'error':'Sequence must be dividable by 3'})

else:
    print json.dumps({'status': 0, 'error': 'Error Parsing Data'})