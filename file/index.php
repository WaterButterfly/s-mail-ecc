<?php
	$root = "..";
	include($root."/common.php");
	
	if ($auth != 1) { header("Location: ".$webp); die; }
	
	$user = $_SESSION["auth"][0];
	
	$i = saferstr($_GET["i"], $unchrs);
	$k = saferstr($_GET["k"], $unchrs);
	$e = saferstr($_GET["e"], $unchrs);
	$f = saferstr($_GET["f"], $unchrs);
	
	$e = preg_replace("/\.trash$/i", "", $e);
	$e = preg_replace("/\.sent$/i", "", $e);
	$e = preg_replace("/\.read$/i", "", $e);
	
	$udir = ($mail."/".$user);
	$emails = scandir($udir);
	foreach ($emails as $email)
	{
		if (($email == ".") || ($email == "..")) { continue; }
		if (preg_match("/^.*auth$/i", $email)) { continue; }
		if (preg_match("/^".$e.".*$/i", $email))
		{
			$data = file_get_contents($udir."/".$email);
			$list = explode("\n", $data);
			if (count($list) > 5)
			{
				$files = explode(" ", trim($list[5]));
				foreach ($files as $file)
				{
					if (file_exists($atch."/".$file) && ($file == $f))
					{
						print("here\n");
						break;
					}
				}
			}
			break;
		}
	}
?>
