<?php
header("Content-Type:text/html; charset=utf-8");
?>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<?php
include("simple_html_dom.php");
set_time_limit(30);
ini_set("memory_limit","2048M");
//require("config.ini");

$arini = parse_ini_file("config.ini");

$key_eu = array();
$eu_fp = fopen("ex_urls.txt", "r");
$eu_i = 0;
while (!feof($eu_fp))
{
	$key_eu[$eu_i] = trim(fgets($eu_fp));
	$eu_i++;
}

function check_exurl($url,$eu)
{
	foreach($eu as $var)
	{
		//echo addcslashes($var,"/");
		if(preg_match("/".addcslashes($var,"/")."/",$url) > 0)
			return true;
	}
	return false;
}

// url,title,summary

$itr = $arini['total_search'];
$num_start = 0;
$total_request = $itr;
$num_entry = $arini['num_search'];
$q = $_GET['q'];
$domain = $arini['search_engine'];

$tkn = MD5($q);
	
if(file_exists("./cache/".$domain."/".$tkn))
{
	$fp = fopen("./cache/".$domain."/".$tkn,"r");
	$q = fgets($fp);
	$content = fread($fp,filesize("./cache/".$domain."/".$tkn));
	fclose($fp);
	print_r(json_decode($content));
	exit;
}

$i = 0;
if($domain=="google")
{

$g_url = "http://www.google.com.tw/search?hl=zh-TW&source=hp&biw=1599&bih=759&q=".$q."&aq=f&aqi=g10&aql=&oq=&num=".$num_entry."&ie=UTF-8&oe=UTF-8";

while(true)
{
	$html = file_get_html($g_url);

	foreach($html->find('.g') as $entry) {
	
		$ur = $entry->find('a', 0)->href;
		if(substr($ur,0,4)!="http")
		{
			continue;
		}
		if($entry->find('.f',0)->plaintext=="檔案類型:")
			continue;
		/*
		$path = parse_url($ur,PHP_URL_PATH);
		$ei = pathinfo($path,PATHINFO_EXTENSION);
		if($ei=="doc" || $ei == "ppt" || $ei == "pdf")
		{
			continue;
		}
		*/
		
		if( check_exurl($ur,$key_eu) )
		{
			//echo "NO";
			continue;
		}
		//echo "OK";
		//echo "<br>";
		
		$ent[$i]['url']     = $ur;
		$ent[$i]['title']   = $entry->find('a', 0)->plaintext;
		$ent[$i]['summary'] = $entry->find('.s',0)->plaintext;
		$i++;
		$total_request--;
		if($total_request <= 0)
			break;
	}
	
	if($total_request <= 0)
		break;	
	$num_start += $num_entry; 
	$g_url = "http://www.google.com.tw/search?hl=zh-TW&source=hp&biw=1599&bih=759&q=".$q."&aq=f&aqi=g10&aql=&oq=&num=".$num_entry."&ie=UTF-8&oe=UTF-8&start=".$num_start;
	
	$html->clear();
}
}else if($domain == "yahoo")
{

$num_start = 0;
$total_request = $itr;

$y_url = "http://tw.search.yahoo.com/search?p=".$q."&fr=yfp&ei=UTF-8&eo=UTF-8&v=0&n=".$num_entry;

while(true)
{
	$html = file_get_html($y_url);
	//$ret = $html->find('h3[r]');
	//$ret = $html->find('div[role]',0);
	
	foreach($html->find('.res') as $entry) {
		if(substr($entry->find('a', 0)->href,0,4)!="http")
			continue;
		//$entry->find('a', 0)->href = null;
		preg_match("/\*\*(.*)/",$entry->find('a', 0)->href,$mat);
		$ur = urldecode($mat[1]);
		if( check_exurl($ur,$key_eu) )
		{
			//echo "NO";
			continue;
		}
		$ent[$i]['url']     = $ur;
		$ent[$i]['title']   = $entry->find('a', 0)->plaintext;
		$ent[$i]['summary'] = $entry->find('.abstr',0)->plaintext;
		$i++;
		$total_request--;
		if($total_request <= 0)
			break;
	}
	if($total_request <= 0)
		break;
	
	$num_start += $num_entry; 
	$y_url = "http://tw.search.yahoo.com/search?p=".$q."&fr=yfp&ei=utf-8&eo=UTF-8&v=0&n=".$num_entry."&b=".$num_start;
}

}else if($domain=="bing")
{
$num_start = 0;
$total_request = $itr;
$num_entry = 10;

$q = urlencode($q);
$b_url = "http://www.bing.com/search?q=".$q."&form=QBLH&count=".$num_entry."&go=&filt=all";

while(true)
{
	
	$html = file_get_html($b_url);
	//$ret = $html->find('h3[r]');
	
	foreach($html->find('.sa_cc') as $entry) {
		if(substr($entry->find('a', 0)->href,0,4)!="http")
			continue;
		$ur = $entry->find('a', 0)->href;
		if( check_exurl($ur,$key_eu) )
		{
			//echo "NO";
			continue;
		}
		$ent[$i]['url']     = $ur;
		$ent[$i]['title']   = $entry->find('a', 0)->plaintext;
		$ent[$i]['summary'] = $entry->find('p',0)->plaintext;
		$i++;
		$total_request--;
		if($total_request <= 0)
			break;
	}
	if($total_request <= 0)
		break;
	$num_start += $num_entry; 
	$b_url = "http://www.bing.com/search?q=".$q."&form=QBLH&count=".$num_entry."&go=&filt=all&first=".$num_start;
}

}
//echo "<pre>";
echo json_encode($ent);
//echo "</pre>";

$fp = fopen("./cache/".$domain."/".$tkn,"w");
fwrite($fp,$q."\n");
fwrite($fp,json_encode($ent));
fclose($fp);

?>