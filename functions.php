<?php

$codonUsage = array("A"=>array("GCG","GCA","GCT","GCC")
            ,"C"=>array("TGT","TGC")
            ,"D"=>array("GAT","GAC")
            ,"E"=>array("GAG","GAA")
            ,"F"=>array("TTT","TTC")
            ,"G"=>array("GGG","GGA","GGT","GGC")
            ,"H"=>array("CAT","CAC")
            ,"I"=>array("ATA","ATT","ATC")
            ,"K"=>array("AAG","AAA")
            ,"L"=>array("TTG","TTA","CTG","CTA","CTT", "CTC")
            ,"M"=>array("ATG")
            ,"N"=>array("AAT","AAC")
            ,"P"=>array("CCG","CCA","CCT","CCC")
            ,"Q"=>array("CAG","CAA")
            ,"R"=>array("AGG","AGA","CGG","CGA","CGT","CGC")
            ,"S"=>array("AGT","AGC","TCG","TCA","TCT","TCC")
            ,"T"=>array("ACG","ACA","ACT","ACC")
            ,"V"=>array("GTG","GTA","GTT","GTC")
            ,"W"=>array("TGG")
            ,"Y"=>array("TAT","TAC")
            ,"Stop"=>array("TGA","TAG","TAA"));

$gallus = array('A'=>array('GCT'=>0.29,'GCC'=>0.32,'GCA'=>0.26,'GCG'=>0.13),
    'F'=>array('TTT'=>0.45,'TTC'=>0.5),
    'Y'=>array('TAT'=>0.40,'TAC'=>0.60),
    'L'=>array('TTA'=>0.08,'TTG'=>0.13,'CTT'=>0.13,'CTC'=>0.18,'CTA'=>0.06,'CTG'=>0.41),
    '*'=>array('TAA'=>0.41,'TGA'=>0.59),
    'C'=>array('TGT'=>0.40,'TGC'=>0.60),
    'S'=>array('TCT'=>0.18,'TCC'=>0.20,'TCA'=>0.15,'TCG'=>0.07,'AGT'=>0.14,'AGC'=>0.26),
    'P'=>array('CCT'=>0.27,'CCC'=>0.30,'CCA'=>0.28,'CCG'=>0.14),
    'Q'=>array('TAG'=>0.01,'CAA'=>0.27,'CAG'=>0.72),
    'W'=>array('TGG'=>1.00),
    'R'=>array('CGT'=>0.10,'CGC'=>0.19,'CGA'=>0.10,'CGG'=>0.18,'AGA'=>0.22,'AGG'=>0.21),
    'H'=>array('CAT'=>0.40,'CAC'=>0.60),
    'N'=>array('AAT'=>0.43,'AAC'=>0.57),
    'T'=>array('ACT'=>0.25,'ACC'=>0.31,'ACA'=>0.30,'ACG'=>0.14),
    'I'=>array('ATT'=>0.35,'ATC'=>0.46,'ATA'=>0.18),
    'M'=>array('ATG'=>1.00),
    'D'=>array('GAT'=>0.50,'GAC'=>0.50),
    'V'=>array('GTT'=>0.21,'GTC'=>0.22,'GTA'=>0.12,'GTG'=>0.45),
    'K'=>array('AAA'=>0.44,'AAG'=>0.56),
    'E'=>array('GAA'=>0.43,'GAG'=>0.57),
    'G'=>array('GGT'=>0.18,'GGC'=>0.31,'GGA'=>0.27,'GGG'=>0.25));

//Input(codon) Return(corresponding Amino Acid)
function getAminoAcid($codon){
  global $codonUsage;
  foreach ($codonUsage as $amino => $codons){
        if(in_array($codon,$codons)) return $amino;
  }
}

//Input(Amino Acid) Return(corresponding Amino Acid)
function getBestCodon($input_amino){
  global $gallus;
  foreach ($gallus as $amino => $codons) {
      if($amino === $input_amino ){
        $bestCodon = max(array_values($codons));
        return array_search ($bestCodon,$codons);
      }
  }
}



function postData($postdata,$url){
    $ch = curl_init();
    $curlConfig = array(
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => $postdata
    );
    curl_setopt_array($ch, $curlConfig);
    $result = curl_exec($ch);
    curl_close($ch);
    return(json_decode($result,true));
}


function redirect($url) {
    if(!headers_sent()) {
        //If headers not sent yet... then do php redirect
        header('Location: '.$url);
        exit;
    } else {
        //If headers are sent... do javascript redirect... if javascript disabled, do html redirect.
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
        exit;
    }
}

function fastaPrint($header="",$body=""){
    $seq1 = $body;
    $x = 0;
    echo $header."<br/>";
    for($i=0;$i<strlen($seq1);$i++){

        if($x == 70){
            $x=0;
            echo "</br>";
        }
        $x+=1;
        echo $seq1[$i];
    }
}


function changeTable($oldSeq,$newSeq){
    global $gallus;
    $changed = array();
    $changeGraph = array();
    for($i=0;$i<(strlen($newSeq)-3);$i+=3){
        $oldCodon = substr($oldSeq,$i,3);
        $newCodon = substr($newSeq,$i,3);
        if($oldCodon != $newCodon){
            array_push($changeGraph,1);
            array_push($changed,array('pos'=>$i,'old'=>$oldCodon,'new'=>$newCodon,'amino'=>getAminoAcid($oldCodon),'fractions'=>$gallus[getAminoAcid($oldCodon)]));
        }else{
            array_push($changeGraph,0);
            continue;
        }
    }
    return array($changeGraph,$changed);
}






function countCG($seq){
    $seq = strtolower($seq);
    $count = array('a' => 0,'c' => 0,'g' => 0,'t' => 0);

    for($i =0; $i < strlen($seq); $i++){
        if((array_key_exists($seq[$i], $count))){
            $count[$seq[$i]] = $count[$seq[$i]] + 1;
        }
    }
    return($count);
}




function generatePieChartChart($id, $cgCount, $title = 'CG - Content'){

    $a = $cgCount['a'];
    $c = $cgCount['c'];
    $g = $cgCount['g'];
    $t = $cgCount['t'];

    echo <<<EOT
    
<script>
    var a = {$a};
    var c = {$c};
    var g = {$g};
    var t = {$t};

    var pie = new d3pie("{$id}", {
        "header": {
            "title": {
                "text": "{$title}",
                "fontSize": 30,
                "font": "open sans"
            },
            "subtitle": {
                "text": "Percentage of each nucleotide ",
                "color": "#999999",
                "fontSize": 16,
                "font": "open sans"
            },
            "titleSubtitlePadding": 9
        },
        "footer": {
            "color": "#999999",
            "fontSize": 10,
            "font": "open sans",
            "location": "bottom-left"
        },
        "size": {
            "canvasWidth": 590,
            "pieOuterRadius": "92%"
        },
        "data": {
            "sortOrder": "value-desc",
            "content": [
                {
                    "label": "A",
                    "value": a,
                    "color": "#cc9fb1"
                },
                {
                    "label": "C",
                    "value": c,
                    "color": "#e65414"
                },
                {
                    "label": "G",
                    "value": g,
                    "color": "#8b6834"
                },
                {
                    "label": "T",
                    "value": t,
                    "color": "#248838"
                }
            ]
        },
        "labels": {
            "outer": {
                "format": "label-value1",
                "pieDistance": 40
            },
            "inner": {
                "hideWhenLessThanPercentage": 3
            },
            "mainLabel": {
                "fontSize": 20
            },
            "percentage": {
                "color": "#ffffff",
                "fontSize": 20,
                "decimalPlaces": 0
            },
            "value": {
                "color": "#adadad",
                "fontSize": 15
            },
            "lines": {
                "enabled": true
            },
            "truncation": {
                "enabled": true
            }
        },
        "effects": {
            "pullOutSegmentOnClick": {
                "effect": "elastic",
                "speed": 400,
                "size": 8
            }
        },
        "misc": {
            "gradient": {
                "enabled": true,
                "percentage": 100
            }
        }
    });
</script>

EOT;




}

