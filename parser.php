<?php

// MATEJ SLIVKA //
// IPP PROJEKT 1 //

// skontroluje ci to je label
function check_label($label)
{
	if (substr_count($label, '@') > 0 ) 
        {
        	error_log("Invalid  label $label \n");
                exit(23);       
        }
}

// skontroluje ci je to variable
function check_var($var)
{
	// musi obsahovat 1 zvinac
	if (substr_count($var, '@') != 1 ) 
        {
        	error_log("Invalid  variable $var \n");
                exit(23);       
        }
		// musi mat LF TF alebo GF
        $variable = explode('@',$var);
        if ($variable[0] != "LF" && $variable[0] != "TF" && $variable[0] != "GF")
        {
        	error_log ("Invalid variable $var \n");
                exit(23);       
        }
		// za @ musi byt alfanumericky znak alebo specialny znak
        $strsplit = str_split($variable[1]);
        if (($strsplit[0] < '0' || $strsplit[0] > '9') && $strsplit[0] != '_' && $strsplit[0] != '-' && $strsplit[0] != '$' && $strsplit[0] != '&' && $strsplit[0] != '%' && $strsplit[0] != '*' && $strsplit[0] != '!' && $strsplit[0] != '?' && ($strsplit[0] < 'A' || $strsplit[0] > 'Z') && ($strsplit[0] < 'a' || $strsplit[0] > 'z') )
        {
        	error_log ("Invalid variable $var \n");
                exit(23);
        }
}

// skontroluje ci je to symbol
function check_symb($symb)
{
		// musi mat 1 vyskyt @
        if (substr_count($symb, '@') != 1 )
        {
                error_log("Invalid  symbol $symb \n");
                exit(23);
        }
		// musi to byt variable string nil bool alebo int
        $variable = explode('@',$symb);
        if ($variable[0] != "LF" && $variable[0] != "TF" && $variable[0] != "GF" && $variable[0] != "string" && $variable[0] != "nil" && $variable[0] != "bool" && $variable[0] != "int")
        {
                error_log ("Invalid symbol $symb \n");
                exit(23);
        }
}

// skontroluje ci je to type
function check_type($type)
{
		// nesmie mat @
        if (substr_count($type, '@') != 0 )
        {
                error_log("Invalid  type $type \n");
                exit(23);
        }
		// musi to byt len strin int alebo bool
	if ($type != "string" && $type != "int" && $type != "bool")
	{
		error_log ("Invalid type $type \n");
		exit(23);
	}
}

// funkcia na vypis xml codu pre  rpikazy s 0 premennymi
function print_instruct_w_0_arg(int $order,$frag)
{
	if (count($frag) != 1)
	{
		error_log ("Invalid number of arguments for $frag[0]\n");
		exit(23);
	}	
	echo "    <instruction order=\"$order\" opcode=\"$frag[0]\">\n";
    echo "    </instruction>\n";
}

// funkcia na vypis xml codu pre  prikazy s 1 premennymi
function print_instruct_w_1_arg(int $order,$frag)
{
	// len 2 argumenty
	if (count($frag) != 2)
        {
                error_log ("Invalid number of arguments for $frag[0]\n");
                exit(23);
        }

        echo "    <instruction order=\"$order\" opcode=\"$frag[0]\">\n";
	// switchni sa podla prikazu
	switch($frag[0])
	{
		case 'DEFVAR':
		case 'POPS':
			// kontorola nech vsetko vyhovuje zadaniu
			if (substr_count($frag[1], '@') != 1 ) 
			{
				error_log("Invalid  variable in $frag[0] \n");
				exit(23);	
			}
			$variable = explode('@',$frag[1]);
                        if ($variable[0] != "LF" && $variable[0] != "TF" && $variable[0] != "GF")
			{
				error_log ("Invalid variable in $frag[0] \n");
				exit(23);	
			}
			$strsplit = str_split($variable[2]);
			if (($strsplit[0] >= '0' && $strsplit[0] <= '9') || ($strsplit[0] != '_' && $strsplit[0] != '-' && $strsplit[0] != '$' && $strsplit[0] != '&' && $strsplit[0] != '%' && $strsplit[0] != '*' && $strsplit[0] != '!' && $strsplit[0] != '?' && $strsplit[0] < 'A' && $strsplit[0] > 'Z' && $strsplit[0] < 'a' && $strsplit[0] > 'z' ))
			{
				error_log ("Invalid variable in $frag[0] \n");
                                exit(23);
			}
			if ($strsplit[0] == '&')
			{
 				$frag[1] = str_replace("&","&amp;",$frag[1]);
				
			}
			echo "        <arg1 type=\"var\">$frag[1]</arg1>\n";
			break;
		case "CALL":
		case "LABEL":
		case "JUMP":
                        if (substr_count($frag[1], '@') > 0 )
                        {
                                error_log("Invalid  argument for $frag[0] \n");
                                exit(23);
                        }
			$strsplit = str_split($frag[1]);
                        if (($strsplit[0] < '0' || $strsplit[0] > '9') && $strsplit[0] != '_' && $strsplit[0] != '-' && $strsplit[0] != '$' && $strsplit[0] != '&' && $strsplit[0] != '%' && $strsplit[0] != '*' && $strsplit[0] != '!' && $strsplit[0] != '?' && ($strsplit[0] < 'A' || $strsplit[0] > 'Z') && ($strsplit[0] < 'a' || $strsplit[0] > 'z') )
			{
                                error_log ("Invalid label in $frag[0] \n");
                                exit(23);
                        }
			echo "        <arg1 type=\"label\">$frag[1]</arg1>\n";
			break;
		case "PUSHS":
		case "WRITE":
		case "DPRINT":
                case "EXIT":
                        if (substr_count($frag[1], '@') != 1)
                        {
                                error_log("Invalid  argument for $frag[0] \n");
                                exit(23);
                        }

			$variable = explode('@',$frag[1]);
			if ($variable[0] == "LF" || $variable[0] == "TF" || $variable[0] == "GF")
			{
				echo "        <arg1 type=\"var\">$variable[0]@";
			}
			else
			{
				echo "        <arg1 type=\"$variable[0]\">";
			}
			$strsplit = str_split($variable[1]);
			$length = strlen($variable[1]);
			for ($i=0; $i<$length; $i++)
			{
        			switch($strsplit[$i])
        			{
                			case '<':
                        			echo "&lt;";
                        			break;
                			case ">":
                        			echo "&gt;";
                       				break;
					case "&":
						echo "&amp;";
						break;
					default:
						echo "$strsplit[$i]";
						break;
			        }

			}
			echo "</arg1>\n";
			break;
	}
        echo "    </instruction>\n";
}

// vypis prikazu s 2 argumentami
function print_instruct_w_2_arg(int $order,$frag)
{
	// skontroluj ci je prva variabe
	check_var($frag[1]);

	// skontroluj argumenty
	if (count($frag) != 3)
        {
                error_log ("Invalid number of arguments for $frag[0]\n");
                exit(23);
        }


        echo "    <instruction order=\"$order\" opcode=\"$frag[0]\">\n";
        switch($frag[0])
        {
        case 'MOVE':
        case 'INT2CHAR':
		case 'STRLEN':
		case 'TYPE':
            echo "        <arg1 type=\"var\">$frag[1]</arg1>\n";
			// skontroluj prvu premennu a drhu symbol
			check_var($frag[1]);	
			check_symb($frag[2]);
			$variable = explode('@',$frag[2]);
                        if ($variable[0] == "LF" || $variable[0] == "TF" || $variable[0] == "GF")
                        {
                                echo "        <arg2 type=\"var\">$variable[0]@$variable[1]</arg2>\n";
                        }
                        else
                        {
                                echo "        <arg2 type=\"$variable[0]\">$variable[1]</arg2>\n";
                        }

                        break;
        case "READ":
			check_var($frag[1]);
			check_type($frag[2]);
                        echo "        <arg1 type=\"var\">$frag[1]</arg1>\n";
			echo "        <arg2 type=\"type\">$frag[2]</arg2>\n";
                        break;
        }
        echo "    </instruction>\n";
}

// vypis prikazu s 3 argumentami
function print_instruct_w_3_arg(int $order,$frag)
{
		//skontroluj pocet argumentov
        if (count($frag) != 4 && $frag[0] != "NOT")
        {
                error_log ("Invalid number of arguments for $frag[0]\n");
                exit(23);
        }

	        if (count($frag) != 3 && $frag[0] == "NOT")
        {
                error_log ("Invalid number of arguments for $frag[0]\n");
                exit(23);
        }

		//skontrluj typ paraetrov
        echo "    <instruction order=\"$order\" opcode=\"$frag[0]\">\n";
        switch($frag[0])
        {
                case 'ADD':
                case 'SUB':
		case 'IDIV':
                case 'LT':
                case 'GT':
                case 'EQ':
                case 'AND':
                case 'OR':
                case 'NOT':
                case 'STRI2INT':
                case 'CONCAT':
                case 'GETCHAR':
                case 'SETCHAR':
                case 'MUL':
		        check_var($frag[1]);
                        check_symb($frag[2]);
			if ($frag[0] != "NOT")
			{
				check_symb($frag[3]);
			}
                        echo "        <arg1 type=\"var\">$frag[1]</arg1>\n";
                        $variable = explode('@',$frag[2]);
                        if ($variable[0] == "LF" || $variable[0] == "TF" || $variable[0] == "GF")
                        {
                                echo "        <arg2 type=\"var\">$variable[0]@$variable[1]</arg2>\n";
                        }
                        else
                        {
                                echo "        <arg2 type=\"$variable[0]\">$variable[1]</arg2>\n";
                        }
                        
			if ($frag[0] != "NOT")
			{
				$variable = explode('@',$frag[3]);
                        	if ($variable[0] == "LF" || $variable[0] == "TF" || $variable[0] == "GF")
                        	{
                                	echo "        <arg3 type=\"var\">$variable[0]@$variable[1]</arg3>\n";
                        	}
                        	else
                        	{
                                	echo "        <arg3 type=\"$variable[0]\">$variable[1]</arg3>\n";
                        	}
                        	break;
			}
			break;
                case "JUMPIFEQ":
                case "JUMPIFNEQ":
                        check_label($frag[1]);
                        check_symb($frag[2]);
                        check_symb($frag[3]);
                        echo "        <arg1 type=\"label\">$frag[1]</arg1>\n";
                        $variable = explode('@',$frag[2]);
                        if ($variable[0] == "LF" || $variable[0] == "TF" || $variable[0] == "GF")
                        {
                                echo "        <arg2 type=\"var\">$variable[0]@$variable[1]</arg2>\n";
                        }
                        else
                        {
                                echo "        <arg2 type=\"$variable[0]\">$variable[1]</arg2>\n";
                        }
                        $variable = explode('@',$frag[3]);
                        if ($variable[0] == "LF" || $variable[0] == "TF" || $variable[0] == "GF")
                        {
                                echo "        <arg3 type=\"var\">$variable[0]@$variable[1]</arg3>\n";
                        }
                        else
                        {
                                echo "        <arg3 type=\"$variable[0]\">$variable[1]</arg3>\n";
                        }
                        break;
        }
        echo "    </instruction>\n";
}


ini_set('display_errors','stderr');

// kontrola parametrov
if ($argc > 2)
{
	error_log("Unexpected number of arguments. $argc were given \n");
	exit(10);
}

// napoveda pre callera
if ($argc == 2 && $argv[1] == "--help" )
{
	echo("Usage: parser.php <InputFile\n");
	exit(0);
}

// kontrola zahlavia
do
{
	$line = fgets(STDIN);
	// odstran koment medzeru a tabulator
	$line = explode('#',$line);
	$line = explode(' ',$line[0]);
	$line = explode('	',$line[0]);
	$line =str_replace("\n","",$line);
}
while ($line[0] == null);

if ($line[0] != ".IPPcode22")
{
	error_log("Missing header: .IPPcode22\n");
	exit(21);
}
else
{
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	echo ("<program language=\"IPPcode22\">\n");
}


$order = 1;

// rozdelenie na prikazy
while ($line = fgets(STDIN))
{
	// odstran koment
	$line = explode('#',$line);
	// odstran \n na konci
	$frag = str_replace("\n","",$line);
	// riadky co su prazdne kvoli tomu ze tam boli komentare preskoc
        if ($frag[0] == null)
        {
                continue;
        }
	
	// vymen tabulator za medzeru
	$frag = str_replace("	"," ",$frag);
	// viacere medzere rataj ako jednu
	$frag = preg_replace('!\s+!', ' ', $frag);
	// rozdel podla medzier
	$frag = explode(' ',$frag[0]);

	//switchni sa podla prikazu
	$frag[0] = strtoupper($frag[0]);
	switch($frag[0])
	{
		// 0 parametrov
		case 'CREATEFRAME':
		case 'PUSHFRAME':
		case 'POPFRAME':
		case 'RETURN':
		case 'BREAK':
			print_instruct_w_0_arg($order,$frag);
			break;
		// 1 parameter
		case 'DEFVAR':
		case 'CALL':
		case 'PUSHS':
		case 'POPS':
		case 'WRITE':
		case 'LABEL':
		case 'JUMP':
		case 'EXIT':
		case 'DPRINT':
			print_instruct_w_1_arg($order,$frag);
			break;
		// 2 parametre
		case 'MOVE':
		case 'INT2CHAR':
		case 'READ':
		case 'STRLEN':
		case 'TYPE':
			print_instruct_w_2_arg($order,$frag);
                        break;
		// 3 parametre
		case 'ADD':
		case 'SUB':
		case 'MUL':
		case 'IDIV':
		case 'LT':
		case 'GT':
		case 'EQ':
		case 'AND':
		case 'OR':
		case 'NOT':
		case 'STRI2INT':
		case 'CONCAT':
		case 'GETCHAR':
		case 'SETCHAR':
		case 'JUMPIFEQ':
		case 'JUMPIFNEQ':
			print_instruct_w_3_arg($order,$frag);
			break;
		default:
			error_log("Use of invalid command.\n");
			exit(22);
	}
	// inkrementacia poradia pre dalsi cyklus
	$order = $order + 1;
}
echo("</program>\n");
exit(0);
?>

