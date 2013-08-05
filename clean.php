<?php
function cleanCache($dirname)
{ 
	//$dirname=preg_replace( "/[^a-zA-Z0-9]/", "", $dirname);
	if ( !file_exists($dirname))
	{
		echo "$dirname is not exist.";
		return false;
	}
  
	$hdir = opendir($dirname);
  
	while( ($file=readdir($hdir))!= false)
	{
		if ($file=='.' OR $file=='..')
		{
			continue;
		}
        else
		{
            unlink($dirname."/".$file);  
        };
    }
  
  closedir($hdir);
  return true;
}

cleanCache("./cache/google/");
cleanCache("./cache/bing/");
cleanCache("./cache/yahoo/");

cleanCache("./article/");
echo "快取清除完成，稍等 1 秒自動回去上頁";
?>
<meta http-equiv="refresh" content="1; URL=javascript:history.back();">