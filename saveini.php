<?php
header("Content-Type:text/html; charset=utf-8");
function write_ini_file($assoc_arr, $path, $has_sections=FALSE) { 
    $content = ""; 
    if ($has_sections) { 
        foreach ($assoc_arr as $key=>$elem) { 
            $content .= "[".$key."]\n"; 
            foreach ($elem as $key2=>$elem2) { 
                if(is_array($elem2)) 
                { 
                    for($i=0;$i<count($elem2);$i++) 
                    { 
                        $content .= $key2."[] = \"".$elem2[$i]."\"\n"; 
                    } 
                } 
                else if($elem2=="") $content .= $key2." = \n"; 
                else $content .= $key2." = \"".$elem2."\"\n"; 
            } 
        } 
    } 
    else { 
        foreach ($assoc_arr as $key=>$elem) { 
            if(is_array($elem)) 
            { 
                for($i=0;$i<count($elem);$i++) 
                { 
                    $content .= $key."[] = \"".$elem[$i]."\"\n"; 
                } 
            } 
            else if($elem=="") $content .= $key." = \n"; 
            else $content .= $key." = \"".$elem."\"\n"; 
        } 
    } 

    if (!$handle = fopen($path, 'w')) { 
        return false; 
    } 
    if (!fwrite($handle, $content)) { 
        return false; 
    } 
    fclose($handle); 
    return true; 
}

$arini = parse_ini_file("./config.ini");

$arini['search_engine'] = $_GET['frm_sg'];
$arini['total_search'] = $_GET['frm_itr'];
$arini['num_search'] = $_GET['frm_num'];
$arini['content_len_beg'] = $_GET['frm_len_beg'];
$arini['content_len_end'] = $_GET['frm_len_end'];
$arini['num_wp'] = $_GET['frm_wp'];
$arini['key_num'] = $_GET['frm_key_num'];
$arini['confest'] = $_GET['frm_confest'];

write_ini_file($arini,"./config.ini");

echo "ini 修改完成，1秒後回前頁。";

?>
<meta http-equiv="refresh" content="1; URL=javascript:history.back();">