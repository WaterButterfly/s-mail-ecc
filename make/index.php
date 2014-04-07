<?php
	$root = "..";
	include($root."/common.php");
	
	if ($auth != 1) { header("Location: ".$webp); die; }
	
	$hpag[0] = "opacity: 1.0 !important;";
	$user = $_SESSION["auth"][0];
	
	$dest = "";
	$eadr = saferstr($_GET["e"], $unchrs."@,;");
	
	if (isset($_POST) && isset($_POST["dest"]))
	{
		if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) { /* no-op */ }
		else
		{
			if (socket_connect($sock, "127.0.0.1", 25) === false) { /* no-op */ }
			else
			{
				$ekey = saferstr($_POST["ekey"], $unchrs." /,;");
				$ekey = str_replace(";", ",", $ekey);
				$elst = explode(",", $ekey);
				
				$dadr = saferstr($_POST["dest"], $unchrs."@,;");
				$dadr = str_replace(";", ",", $dadr);
				$dlst = explode(",", $dadr);
				
				$subj = saferstr($_POST["subj"], " ~!@#%^&*-=+_<>[]{}();:,.?/`'\$\"\\");
				$auma = trim(file_get_contents($mail."/"."server.auth"));
				
				$desc = ("\n".$_POST["mesg"]."\n");
				$desc = str_replace("\r", "", $desc);
				$desc = str_replace("\n.\n", "\n . \n", $desc);
				
				$dbeg = ("EHLO goodsir\n");
				$dbeg .= ("AUTH PLAIN ".$auma."\n");
				$dbeg .= ("MAIL FROM: ".$user."@".$name."\n");
				
				foreach ($dlst as $item)
				{
					if (strpos($item, "@") !== false)
					{
						$dbeg .= ("RCPT TO: ".$item."\n");
					}
				}
				
				$dbeg .= ("DATA\n");
				
				$dmid = ("Zsflag: true\n");
				$dmid .= ("From: ".$user."@".$name."\n");
				$dmid .= ("To: ".$dadr."\n");
				$dmid .= ("Subject: ".$subj."\n");
				
				$fileleng = count($_FILES["attach"]["name"]);
				if ($fileleng > 0)
				{
					$dmid .= ("Content-Type: multipart/mixed; boundary="."047d7b2e4016d24ce204f63e8b83"."\n");
				}
				
				$dmid .= ("\n");
				
				if ($_POST["type"] == "emsg")
				{
					foreach ($elst as $item)
					{
						$info = explode(" ", $item);
						if (count($info) > 2)
						{
							$dmid .= ("Zsmsg-".$info[0].": ".$item."\n");
						}
					}
					$dmid .= ("\n");
				}
				
				$dend = (trim($desc)."\n");
				
				$data = ($dbeg.$dmid.$dend.".\n"."QUIT\n");
				socket_write($sock, $data, strlen($data));
				socket_close($sock);
				
				$data = ($dmid.$dend);
				$filedesc = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("file", "/dev/null", "w"));
				$fileproc = proc_open($post." sent", $filedesc, $filepipe, "/tmp", array());
				if (is_resource($fileproc))
				{
					fwrite($filepipe[0], $data);
					fclose($filepipe[0]);
					fclose($filepipe[1]);
					proc_close($fileproc);
				}
				
				print("<script>window.history.go(-2);</script>"); die;
			}
		}
	}
?>

<html>
	<script>
		var anum = 0, init = 0;
		function addattach()
		{
			if (anum != init)
			{
				jQuery('#attachg').append("<div id='rattach"+anum+"' class='form-group'><label for='inputhatch' class='col-sm-2 control-label'> &nbsp; </label><div class='col-sm-6 input-group-sm'><span id='file"+anum+"'></span></div></div>");
			}
			tnum = anum;
			if (anum == init) { tnum = 0; }
			jQuery('#file'+tnum).html("<a href='javascript:remattach("+anum+");' class='txtred'><span class='glyphicon glyphicon-remove-circle' style='top: 2px;'></span></a> &nbsp; <input type='file' name='attach[]' style='display: inline;' />");
			anum += 1;
		}
		function remattach(rnum)
		{
			if (rnum == init)
			{
				var srcid = "";
				jQuery('span').each(function() {
					if (srcid == "") {
						if (jQuery(this).attr('id')) {
							if (jQuery(this).attr('id').match(/^file[1-9][0-9]*$/)) {
								if (jQuery(this).attr('id') != ("file" + init)) {
									srcid = jQuery(this).attr('id');
								}
							}
						}
					}
				});
				if (srcid != "")
				{
					jQuery('#file0').html(jQuery('#'+srcid).html());
					init = parseInt(srcid.replace(/[^0-9]/g, ""), 10);
					rnum = init;
				}
				else
				{
					jQuery('#file0').html("");
					init = anum;
				}
			}
			jQuery('#rattach'+rnum).remove();
		}
	</script>
	
	<?php include($root."/html/head.html"); ?>
	
	<body onload="jQuery('#dest').focus(); getkeyi(user, '#keyi'); procdest({'keyCode':21});">
		<?php include($root."/html/menu.html"); ?>
		
		<table style="width: 100%;">
			<tr>
				<td style="white-space: nowrap; vertical-align: top; padding-left: 5px; padding-right: 5px;"><span id="pkeys">
					<center> &nbsp; <br /> &nbsp; <br /> &nbsp; <br />Enter a "To" Address &nbsp; <span class="glyphicon glyphicon-arrow-right" style="top: 2px;"></span></center>
				</span></td>
				
				<td style="width: 80%; padding-right: 5px;">
					<form id="send" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
					<input type="hidden" name="type" id="type" value="mail" />
					<input type="hidden" name="ekey" id="ekey" value="" />
						<table style="width: 100%;">
							<tr><td>
								<div class="panel panel-primary">
									<div class="panel-heading" style="padding-top: 3px; padding-bottom: 3px;">
										<table style="width: 100%;"><tr>
											<th style="text-align: left; width: 30%;"><a style="color: white !important;" href="javascript:window.history.back();"><span class="glyphicon glyphicon-circle-arrow-left" style="top: 2px;"></span> Back</a></th>
											<th style="text-align: center;">New Message from &nbsp; <i>[ <?php print($user."@".$name); ?> ]</i></th>
											<th style="text-align: right; width: 30%;"><button type="button" class="btn btn-sm btn-warning" onclick="subsend();" style="float: right; margin-right: 14px;">Send</button></th>
										</tr></table>
									</div>
									<div class="panel-body" style="color: #333;">
										<div class="form-group">
											<label for="inputdest" class="col-sm-2 control-label">To</label>
											<div class="col-sm-6 input-group-sm">
												<input type="text" name="dest" id="dest" class="form-control" placeholder="user@host.com, ..." value="<?php print($eadr); ?>" onkeyup="procdest(event);" />
											</div>
											<label for="inputsend" class="col-sm-3 control-label"> &nbsp; </label>
										</div>
										<div class="form-group">
											<label for="inputhinta" class="col-sm-2 control-label">Helper &nbsp; <span class="glyphicon glyphicon-circle-arrow-right" style="top: 2px;"></span></label>
											<label for="inputhintb" class="col-sm-6 control-label" style="text-align: left;"><span id="hint"> &nbsp; </span></label>
										</div>
										<div class="form-group">
											<label for="inputsubj" class="col-sm-2 control-label">Subject</label>
											<div class="col-sm-10 input-group-sm">
												<input type="text" name="subj" id="subj" class="form-control" placeholder="Subject" onfocus="jQuery('#hint').text('');" />
											</div>
										</div>
										<div class="form-group">
											<label for="inputhatch" class="col-sm-2 control-label" style="padding-top: 1px;"><a href="javascript:addattach();" class="txtgreen">Attach <span class="glyphicon glyphicon-plus-sign" style="top: 2px;"></span></a></label>
											<div class="col-sm-6 input-group-sm">
												<span id="file0"></span>
											</div>
										</div>
										<span id="attachg"></span>
										<div class="form-group">
											<div class="col-sm-12 input-group-sm">
												<textarea name="mesg" id="mesg" class="form-control" placeholder="Message" style="height: 512px;" onfocus="jQuery('#hint').text('');"></textarea>
											</div>
										</div>
									</div>
								</div>
							</td></tr>
						</table>
					</form>
				</td>
			</tr>
		</table>
		
		<?php include($root."/html/foot.html"); ?>
	</body>
</html>
