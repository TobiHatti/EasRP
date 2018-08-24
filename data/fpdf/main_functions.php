<?php
    require('data/fpdf/fpdf.php');
    require('data/fpdf/makefont/makefont.php');

    class PDF extends FPDF
    {
        protected $B = 0;
        protected $I = 0;
        protected $U = 0;
        protected $HREF = '';

        // Page header
        function Header()
        {
            $this->Image('files/content/'.GetProperty("company_logo"),130,10,60);
            $this->Ln(25);
        }

        // Page footer
        function Footer()
        {
            // Position from bottom
            $this->SetY(-20);

            /*
            $this->SetFont('Arial','U',9);
            $this->Cell(20,6,"Bankverbindung:",0,1,'L');
            $this->SetFont('Arial','',9);
            $this->Cell(33,4,"Empfänger:",0,0,'L');
            $this->Cell(50,4,"?????",0,1,'L');
            $this->Cell(33,4,"Verwendungszweck:",0,0,'L');
            $this->Cell(80,4,"?????",0,1,'L');
            $this->Cell(33,4,"IBAN:",0,0,'L');
            $this->Cell(50,4,"?????",0,1,'L');
            $this->Cell(33,4,"BIC:",0,0,'L');
            $this->Cell(50,4,"?????",0,1,'L');
            */


            // Arial italic 8
            $this->SetFont('Arial','I',8);
            // Page number
            $this->Cell(0,5,'Seite '.$this->PageNo().'/{nb}',0,1,'C');
            //$this->Cell(0,5,'Seite 1/1',0,1,'C');
            $this->Cell(0,5,((GetProperty("allow_tax_deduct")) ? 'Dies ist keine Rechnung nach § 11 UStG, daher ist der Käufer nicht zum Vorsteuerabzug berechtigt!' : ''),0,1,'C');
        }

        function WriteHTML($html)
        {
            // HTML parser
            $html = str_replace("\n",' ',$html);
            $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
            foreach($a as $i=>$e)
            {
                if($i%2==0)
                {
                    // Text
                    if($this->HREF)
                        $this->PutLink($this->HREF,$e);
                    else
                        $this->Write(5,$e);
                }
                else
                {
                    // Tag
                    if($e[0]=='/')
                        $this->CloseTag(strtoupper(substr($e,1)));
                    else
                    {
                        // Extract attributes
                        $a2 = explode(' ',$e);
                        $tag = strtoupper(array_shift($a2));
                        $attr = array();
                        foreach($a2 as $v)
                        {
                            if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                                $attr[strtoupper($a3[1])] = $a3[2];
                        }
                        $this->OpenTag($tag,$attr);
                    }
                }
            }
        }

        function OpenTag($tag, $attr)
        {
            // Opening tag
            if($tag=='B' || $tag=='I' || $tag=='U')
                $this->SetStyle($tag,true);
            if($tag=='A')
                $this->HREF = $attr['HREF'];
            if($tag=='BR')
                $this->Ln(5);
        }

        function CloseTag($tag)
        {
            // Closing tag
            if($tag=='B' || $tag=='I' || $tag=='U')
                $this->SetStyle($tag,false);
            if($tag=='A')
                $this->HREF = '';
        }

        function SetStyle($tag, $enable)
        {
            // Modify style and select corresponding font
            $this->$tag += ($enable ? 1 : -1);
            $style = '';
            foreach(array('B', 'I', 'U') as $s)
            {
                if($this->$s>0)
                    $style .= $s;
            }
            $this->SetFont('',$style);
        }

        function PutLink($URL, $txt)
        {
            // Put a hyperlink
            $this->SetTextColor(0,0,255);
            $this->SetStyle('U',true);
            $this->Write(5,$txt,$URL);
            $this->SetStyle('U',false);
            $this->SetTextColor(0);
        }

        function ImprovedTable($header, $data)
        {
            // Column widths
            $w = array(40, 35, 40, 45);
            // Header
            for($i=0;$i<count($header);$i++)
                $this->Cell($w[$i],7,$header[$i],1,0,'C');
            $this->Ln();
            // Data
            foreach($data as $row)
            {
                $this->Cell($w[0],6,$row[0],'LR');
                $this->Cell($w[1],6,$row[1],'LR');
                $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
                $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
                $this->Ln();
            }
            // Closing line
            $this->Cell(array_sum($w),0,'','T');
        }
    }
?>