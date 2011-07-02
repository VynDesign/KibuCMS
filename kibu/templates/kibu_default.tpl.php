<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title><?=$pageTitle;?></title>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=iso-8859-1" />
		<meta name="description" content="<?=$metaDesc;?>" />
		<meta name="keywords" content="<?=$metaKeywds;?>" />
		<script src="/kibu/core/util/JS/prototype.js" type="text/javascript"></script>
		<script src="/kibu/core/util/JS/scriptaculous.js?load=builder,effects" type="text/javascript"></script>
		<script src="/kibu/core/util/JS/modalbox.js" type="text/javascript"></script>
		<script src="/kibu/core/util/JS/unittest.js" type="text/javascript"></script>
		<link rel="stylesheet" href="/kibu/core/styles/kibu_core.css" type="text/css" />
		<link rel="stylesheet" href="/kibu/core/styles/modalbox.css" type="text/css" />
		<link rel="stylesheet" href="/kibu/core/styles/kibu_default.css" type="text/css" />
		<?=$additionalHead;?>
	</head>
	<body <?=$bodyExtra;?>>
			<div id="header">
					<div class="container">
							<div class="welcomeMessage">
									<?=$welcomeMessage;?>
							</div>
							<h1 class="sitetitle"><a href="/"><?=$siteTitle;?></a></h1>
							<h2 class="sitetagline"><?=$siteTagLine;?></h2>
					</div>
			</div>
			<div id="topNav">
				<?=$globalNav;?>
			</div>
			<div id="content">
					 <div class="container">
							<div id="sidebar">
									<?=$currentNav;?>
										<?=@$globalContent[1];?>
										<?=@$globalContent[2];?>
										<?=@$globalContent[3];?>
										<?=@$globalContent[4];?>
							</div>
							<div id="body">
										<?=$editorToolbar;?>
										<?=$pageContent[1];?>
							</div>
					</div>
					<br class="clear" />
			</div>
			<div id="footer">
					<div class="container">
							<span class="copyright">All content &copy; <?=$year;?> <a href="/"><?=$siteOwner;?></a>. All rights reserved.</span>
							<br class="clear" />
					</div>
			</div>
			<?=$additionalFoot;?>
	</body>
</html>