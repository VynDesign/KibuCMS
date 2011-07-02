
				<fieldset class="half left">
						<legend>Site Title</legend>
						<label for="siteTitle">
								<input class="full" type="text" name="siteTitle" id="siteTitle" value="<?=$siteTitle;?>" /><br />
								Used as top-level heading of the site.
						</label>
				</fieldset>
				<fieldset class="half right">
						<legend>Site Tagline</legend>
						<label for="siteTagLine">
								<input class="full" type="text" name="siteTagLine" id="siteTagLine" value="<?=$siteTagLine;?>" /><br />
								Used as secondary heading of the site.
						</label>
				</fieldset>
				<fieldset class="half left">
						<legend>Site Owner</legend>
						<label for="siteTitle">
								<input class="full" type="text" name="siteOwner" id="siteOwner" value="<?=$siteOwner;?>" /><br />
								Individual or Organization that owns and/or operates this site (copyright info).
						</label>
				</fieldset>
				<fieldset class="half right">
						<legend>Email Address</legend>
						<label for="siteTitle">
								<input class="full" type="text" name="siteEmail" id="siteEmail" value="<?=$siteEmail;?>" /><br />
								Address used on all emails sent from this site (registration confirmation emails, etc).
						</label>
				</fieldset>
				<h5>Server Settings</h5>
						<label class="warning">WARNING: Changing these settings can possibly adversely affect the way your site works. Change these values only if you absolutely need to.</label><br />
						<fieldset class="half left">
								<legend>Site Domain Name</legend>
								<label for="siteAddress">
										<input class="full" type="text" name="siteAddress" id="siteAddress" value="<?=$siteAddress;?>" /><br />
										Full domain name of the site. This value is used to load the correct navigation and content for this site.
								</label>
						</fieldset>
						<fieldset class="half left">
								<legend>Site Cookie Name</legend>
								<label for="cookiePrefix">
										<input class="full" type="text" name="cookiePrefix" id="cookiePrefix" value="<?=$cookiePrefix;?>" /><br />
										String of characters (can be a word) affixed before all system cookies to make them unique to this site.
								</label>
						</fieldset>
						<fieldset class="half left">
								<legend>Site Default Template</legend>
								<label for="templateMasterDefault">
										<select name="templateMasterDefault" id="templateMasterDefault">
												<?foreach($siteTemplates as $key => $value):?>
												<option value="<?=$key;?>" <?if($templateMasterDefault == $key) {echo "selected=\"selected\"";}?>><?=$value;?></option>
												<?endforeach;?>
										</select><br />
										Design template used on all pages unless otherwise specified in the page creation process.
								</label>
						</fieldset>
						<fieldset class="half left">
								<legend>Site Default Landing Section</legend>
								<label for="siteDefaultSectionNum">
										<select name="siteDefaultSectionNum" id="siteDefaultSectionNum">
												<?foreach($siteSections as $key => $value):?>
												<option value="<?=$key;?>" <?if($siteDefaultSectionNum == $key) {echo "selected=\"selected\"";}?>><?=$value;?></option>
												<?endforeach;?>
										</select><br />
										Section of the site that is loaded when a visitor accesses the domain name without a section or page specified.
								</label>
						</fieldset>
				<br class="clear" />
