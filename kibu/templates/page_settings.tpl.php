			<fieldset class="left half">
				<legend>Content Title</legend>
				<label for="contentTitle"><input class="full" name="contentTitle" id="contentTitle" type="text" value="<?=$contentTitle; ?>" />
				Title of this page (used in browser's "Title bar")</label>
			</fieldset>	
			<fieldset class="right half">
				<legend>Content Link</legend>
				<label for="titleClean"><input class="full" name="titleClean" id="titleClean" type="text" value="<?=$titleClean; ?>" />
				Used in the URL as the filename. Determined by content title if left blank.</label>
			</fieldset>		
			<br class="clear" />
			<fieldset class="admin half left">
				<legend>Content Keywords</legend>
				<label for="metaKeywds">
					<textarea class="full" name="metaKeywds" id="metaKeywds" cols="20" rows="20" style="width:100%; height:60px;"><?=$metaKeywds;?></textarea>
					Used by search engines. Separate key words by a comma followed by a space only.
				</label>
			</fieldset>
			<fieldset class="admin half right">
				<legend>Content Description</legend>
				<label for="metaDesc">
					<textarea class="full" name="metaDesc" id="metaDesc" cols="20" rows="20" style="width:100%; height:60px;"><?=$metaDesc;?></textarea>
					Used by search engines. Write a very short description of the contents of the page.
				</label>
			</fieldset>

				<fieldset class="clear">
						<legend>Page Display/Navigation Settings</legend>
						<label class="small left" for="sectionID">Site Section<br />
					<select size="1" name="sectionID" id="sectionID">
						<option value="">:: Choose ::</option>
						<?foreach($sections as $section):?>
						<option value="<?=$section['sectionID'];?>"<?php if ($sectionID == $section['sectionID']) {echo " selected=\"selected\"";} ?>><?=$section['sectionFullName'];?></option>
						<?endforeach;?>
					</select>
				</label>

				<label class="small left" for="siteTemplateID">Page Template<br />
					<select size="1" name="siteTemplateID">
						<option value="">:: Choose ::</option>
						<?foreach($siteTemplates as $templateID => $templateName):?>
						<option value="<?=$templateID;?>"<?php if ($siteTemplateID == $templateID) {echo " selected=\"selected\"";} ?>><?=$templateName;?></option>
						<?endforeach;?>
					</select>
				</label>

				<label class="small left" for="isVisible">Visibility<br />
					<select size="1" name="isVisible" id="isVisible">
						<option value="">:: Choose ::</option>
						<option value="vis"<?php if ($isVisible == 'vis') {echo " selected=\"selected\"";} ?>>Visible</option>
						<option value="invis"<?php if ($isVisible == 'invis') {echo " selected=\"selected\"";} ?>>Invisible</option>
            <option value="inac"<?php if($isVisible =='inac') {echo " selected=\"selected\"";}?>>Inaccessible</option>
					</select>
				</label>
				</fieldset>

				<fieldset class="clear">
						<legend>Authorization Settings</legend>
						<label class="small left" for="visitorAuthLevel">Viewer Authorization<br />
								<select size="1" name="visitorAuthLevel" id="visitorAuthLevel">
										<option value="">:: Choose ::</option>
										<?foreach($userLevels as $levelNum => $levelName):?>
										<option value="<?=$levelNum;?>"<?php if ($visitorAuthLevel == $levelNum) {echo " selected=\"selected\"";} ?>><?=$levelName;?></option>
										<?endforeach;?>
								</select>
						</label>
				
						<label class="small left" for="editorAuthLevel">Editor Authorization<br />
								<select size="1" name="editorAuthLevel" id="editorAuthLevel">
										<option value="">:: Choose ::</option>
										<?foreach($userLevels as $levelNum => $levelName):?>
										<option value="<?=$levelNum;?>"<?php if ($editorAuthLevel == $levelNum) {echo " selected=\"selected\"";} ?>><?=$levelName;?></option>
										<?endforeach;?>
								</select>
						</label>
						<input type="hidden" name="contentRecordNum" id="contentRecordNum" value="<?=$contentRecordNum;?>" />
				</fieldset>
			