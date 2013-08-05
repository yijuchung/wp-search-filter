<?php
header("Content-Type:text/html; charset=utf-8");
$s_fp = fopen("search_keywords.txt", "w");
$eu_fp = fopen("ex_urls.txt", "w");
$a_fp = fopen("article_keywords.txt", "w");
$e_fp = fopen("ex_article_keywords.txt", "w");
//echo $_POST['frm_exurl'];
fwrite($s_fp,$_POST['frm_key']);
fwrite($eu_fp,$_POST['frm_exurl']);
fwrite($a_fp,$_POST['frm_akey']);
fwrite($e_fp,$_POST['frm_exkey']);

fclose($s_fp);
fclose($eu_fp);
fclose($a_fp);
fclose($e_fp);

echo "data 修改完成，稍等 1 秒自動回去上頁";

?>
<meta http-equiv="refresh" content="1; URL=javascript:history.back();">