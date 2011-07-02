<? echo "<?"; ?>xml version="1.0" encoding="ISO-8859-1"<? echo "?>"; ?> 
	<feed xmlns="http://www.w3.org/2005/Atom">
		<title><?=$pageTitle;?> - Atom Feed</title>
		<subtitle><?=$siteLongDesc;?></subtitle>
		<link rel="self" href="http://<?=$siteAddress;?>/feed/atom/"/>
		<id>http://<?=$siteAddress;?>/feed/atom/</id>
		<updated><?=$dateNow;?>T<?=$timeNow;?>Z</updated>
		<author>
			<name><?=$siteAddress;?></name>
			<email><?=$siteEmail;?></email>
		</author>
		<?=$body;?>
	</feed>