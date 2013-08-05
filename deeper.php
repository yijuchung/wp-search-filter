<?php
header("Content-Type:text/html; charset=utf-8");
set_time_limit(0);
ini_set("memory_limit","2048M");
include("simple_html_dom.php");

$arini = parse_ini_file("config.ini");

$max_string_beg = $arini['content_len_beg'];
$max_string_end = $arini['content_len_end'];
$max_key = $arini['key_num'];
$max_string = $max_string_beg+$max_string_end;
$domain = $arini['search_engine'];
mb_internal_encoding("UTF-8");

// key
// result[]
//// url
//// title
//// summary
//// content
//// key_parse[]

function objectToArray( $object )
{
    if( !is_object( $object ) && !is_array( $object ) )
    {
        return $object;
    }
    if( is_object( $object ) )
    {
         $object = get_object_vars( $object );
    }
    return array_map( 'objectToArray', $object );
}

$k_i = 0;
$key_ag = array();
$k_fp = fopen("article_keywords.txt", "r");
while (!feof($k_fp))
{
	$key_ag[$k_i] = trim(fgets($k_fp));
	$k_i++;
}

function strtrim($string,$ag,$max_string_beg,$max_string,$max_key)
{
	$static_string = $string;
	foreach($ag as $var)
	{
		 $string = $static_string;
		//echo $string;
		//$temp_pos = 0;
		//$temp_string = "";
		$ll = mb_strlen($var);
		echo $var.":<br>";
					
		//$temp_pos = mb_strpos($string,$var);
		//echo "/";
		//if($temp_pos != 0)
		//	echo $temp_string = mb_substr($string,$temp_pos,$max);
		//echo "<br>";
		//$temp[] = trim($temp_string," ");

		do
		{
			$temp_pos = mb_strpos($string,$var);
			//echo "/";
			if($temp_pos == 0)
				break;
			//echo "Begin from (".($temp_pos-$max_string_beg).")";
			$temp_string = mb_substr($string,$temp_pos-$max_string_beg,$max_string+$ll);
			
			$string = mb_substr($string,$temp_pos+$ll+$max_string_end-1);
			//$temp_string = trim($temp_string);
			
			$order   = array("\r\n", "\n", "\r", " ");
			$temp_string = str_replace($order, "", $temp_string);
			$temp_string = trim($temp_string);
			
			if($temp_string != null && mb_strlen($temp_string) > $ll )
			{
				$temp[] = $temp_string;
				echo $temp_string;
				echo "<br>";
			}
			unset($temp_string);
			if( count($temp) >= $max_key )
				break;
		}
		while(true);
		$temp2[$var] = $temp;
		unset($temp);
		
	}
	return $temp2;
}

$key_ea = array();
$e_fp = fopen("ex_article_keywords.txt", "r");

$e_i = 0;
while (!feof($e_fp))
{
	$key_ea[$e_i] = trim(fgets($e_fp));
	$e_i++;
}

function filter($string,$ea)
{
	foreach($ea as $var)
	{
		//echo $var;
		$string = mb_ereg_replace($var,"***",$string);
	}
	//$string = $str_replace("","",$string);
	return $string;
}


$data = array();
$dir = opendir("./cache/".$domain."/");
$i = 0;
while (false !== ($file = readdir($dir)))
{
    if($file=="." || $file=="..")
		continue;
	//echo $file;
	$fp = fopen("./cache/".$domain."/".$file,"r");
	$data[$i]['key'] = fgets($fp);
	$data[$i]['result'] = objectToArray(json_decode(fread($fp,filesize("./cache/".$domain."/".$file))));
	//$i;
	//print_r($data[$i]['result']);
	fclose($fp);
	$i++;
}

//$i--;
$max_file = $i;

//print_r($data);
for($j = 0;$j<$max_file;$j++)
{
	if(!isset($data[$j]['result']))
	{
		//echo $j;
		continue;
	}
	foreach($data[$j]['result'] as $var)
	{
		echo "URL:".$var['url'];
		echo "<br>";

		$filename = "./article/".MD5($var['url'])."_".$domain.".txt";
		if(file_exists($filename))
		{
			$fp = fopen($filename,"r");
			$content = fread($fp,filesize($filename));
			
			$arr = json_decode($content);
			$arr = objectToArray($arr);
			echo $arr['content'];
			echo $arr['url']."<br>";
			
			foreach($arr['key_parse'] as $kk => $vv )
			{
				if($vv != null)
				{
					foreach($vv as $qq )
						echo $kk.":".$qq."<br>";
				}
			}
			//print_r($arr['key_parse']);
			
			echo "<br>";
			fclose($fp);
			continue;
		}
			
		$html = file_get_html($var['url']);
		$encode = mb_detect_encoding($html->plaintext,array("UTF-8","BIG5","GBK","GB2312"));
		if( $encode != "UTF-8" )
			echo $var['content'] = iconv($encode,"UTF-8//IGNORE",$html->plaintext);
		else
			echo $var['content'] = $html->plaintext;
			
		$var['content'] = filter($var['content'],$key_ea);
		echo $var['url']."<br>";
		$var['key_parse'] = array();
		$var['key_parse'] = strtrim($var['content'],$key_ag,$max_string_beg,$max_string,$max_key);
		
		$var['key'] = $data[$j]['key'];
		
		//print_r($var['key_parse']);
		$fp = fopen($filename,"w");
		fwrite($fp,json_encode($var));
		fclose($fp);
		echo "<br>";
		$html->clear();
	}
}
echo "深度搜尋完成，1秒後回前頁。";

?>
<meta http-equiv="refresh" content="1; URL=javascript:history.back();">