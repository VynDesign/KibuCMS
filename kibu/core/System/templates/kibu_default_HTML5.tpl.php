<!DOCTYPE html>
<html>
	<head>
		<title><?=$pageTitle;?></title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta charset="utf-8" />
		<meta name="description" content="<?=$metaDesc;?>" />
		<meta name="keywords" content="<?=$metaKeywds;?>" />
		<script src="/kibu/core/util/JS/jquery-1.6.min.js"></script>
		<script src="/kibu/core/util/JS/modernizr-latest.js"></script>		
		<script src="/kibu/core/util/JS/fancybox/jquery.fancybox-1.3.4.pack.js"></script>		
		<link rel="stylesheet" href="/kibu/core/styles/kibu_core.css" type="text/css" />
		<link rel="stylesheet" href="/kibu/core/styles/kibu_default.css" type="text/css" />
		<link rel="stylesheet" href="/kibu/core/styles/jquery.fancybox-1.3.4.css" media="screen" />		
		<?=$additionalHead;?>
	</head>
	<body <?=$bodyExtra;?>>
		<header id="header">
			<div class="welcomeMessage">
				<?=$welcomeMessage;?>
			</div>
			<hgroup>
				<h1 class="sitetitle"><a href="/"><?=$siteTitle;?></a></h1>
				<h2 class="sitetagline"><?=$siteTagLine;?></h2>
			</hgroup>
		</header>
		<nav id="topNav">
			<?=$globalNav;?>
		</nav>
		<section id="content">
			<?=$editorToolbar;?>				
			<?=$pageContent;?>
			<br class="clear" />
		</section>
		<footer id="footer">
			<span class="copyright">All content &copy; <?=$year;?> <a href="/"><?=$siteOwner;?></a>. All rights reserved.</span>
			<br class="clear" />
		</footer>
		<?=$additionalFoot;?>
	</body>
</html>