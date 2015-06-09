
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
					<label for="siteOwner">
						<input class="full" type="text" name="siteOwner" id="siteOwner" value="<?=$siteOwner;?>" /><br />
						Individual or Organization that owns and/or operates this site (copyright info).
					</label>
				</fieldset>
				<fieldset class="half right">
					<legend>Email Address</legend>
					<label for="siteEmail">
						<input class="full" type="text" name="siteEmail" id="siteEmail" value="<?=$siteEmail;?>" /><br />
						Address used on all emails sent from this site (registration confirmation emails, etc).
					</label>
				</fieldset>
				<fieldset class="half left">
					<legend>Site Master Template</legend>
					<label for="templateMasterDefault">
						<select name="templateMasterDefault" id="templateMasterDefault">
							<?=$siteTemplates;?>
						</select><br />
						Design template used on all pages unless otherwise specified in section settings.
					</label>
				</fieldset>
				<fieldset class="half right">
					<legend>Site Default Landing Section</legend>
					<label for="siteDefaultSectionNum">
						<select name="siteDefaultSectionNum" id="siteDefaultSectionNum">
							<?=$siteSections;?>
						</select><br />
						Section of the site that is loaded when a visitor accesses the domain name without a section or page specified.
					</label>
				</fieldset>
				<br clas="clear" />
				<fieldset class="clear full">
					<legend>Postal Address</legend>
					<label for="address">
						<textarea id="address" name="address" cols="100" rows="10"><?=$address;?></textarea>
						Mailing address for your organization.
					</label>
				</fieldset>
