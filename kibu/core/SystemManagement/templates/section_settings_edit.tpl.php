
				<fieldset class="left half">
					<legend>Section Display Name</legend>
					<label for="sectionFullName">
						<input type="text" class="full" name="sectionFullName" id="sectionFullName" value="<?=$sectionFullName;?>" />
						Name of this section displayed in navigation and title bar
					</label>
				</fieldset>
				<fieldset class="right half">
					<legend>Section Link</legend>
					<label for="sectionName">
						<input type="text" class="full" name="sectionName" id="sectionName" value="<?=$sectionName;?>" />
						Used in the URL as the directory name. Determined by section name if left blank
					</label>
				</fieldset>
				<fieldset class="clear">
					<legend>Display/Navigation Settings</legend>
					<label class="small left" for="sectionSiteTemplate">Section Master Template<br />
						<select name="sectionSiteTemplate" id="sectionSiteTemplate">
							<?=$siteTemplates;?>
						</select>
					</label>					
					
					<label class="small left" for="landingPageRecordNum">Default Landing Page<br />
						<select size="1" name="landingPageRecordNum" id="landingPageRecordNum">
							<option value="">:: Choose ::</option>
							<?=$sectionPages;?>
						</select>
					</label>
					<label class="small left" for="sectionVisible">Visibility<br />
						<select size="1" name="sectionVisible" id="sectionVisible">
							<option value="">:: Choose ::</option>
							<option value="y" <?php if ($sectionVisible == 'y') {echo " selected=\"selected\"";} ?>>Visible</option>
							<option value="n" <?php if ($sectionVisible == 'n') {echo " selected=\"selected\"";} ?>>Hidden</option>
						</select>
					</label>
					<input type="hidden" name="sectionNum" id="sectionNum" value="<?=$sectionNum;?>" />						
				</fieldset>
