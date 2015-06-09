

			<fieldset class="left half">
				<legend>Page Title</legend>
				<label for="contentTitle"><input class="full" name="contentTitle" id="contentTitle" type="text" value="<?=$contentTitle; ?>" />
				Title of this page (used in browser's "Title bar")</label>
			</fieldset>	
			<fieldset class="right half">
				<legend>Page Link</legend>
				<label for="titleClean"><input class="full" name="titleClean" id="titleClean" type="text" value="<?=$titleClean; ?>" />
				Used in the URL as the filename. Determined by content title if left blank.</label>
			</fieldset>		
			<br class="clear" />
			
			<fieldset class="admin half left">
				<legend>Page Keywords</legend>
				<label for="metaKeywds">
					<textarea class="full" name="metaKeywds" id="metaKeywds" cols="20" rows="20" style="width:100%; height:60px;"><?=$metaKeywds;?></textarea>
					Used by search engines. Separate key words by a comma followed by a space only.
				</label>
			</fieldset>
			<fieldset class="admin half right">
				<legend>Page Description</legend>
				<label for="metaDesc">
					<textarea class="full" name="metaDesc" id="metaDesc" cols="20" rows="20" style="width:100%; height:60px;"><?=$metaDesc;?></textarea>
					Used by search engines. Write a very short description of the contents of the page.
				</label>
			</fieldset>

			<fieldset class="clear">
				<legend>Page Display/Navigation Settings</legend>
				<label class="small left" for="isVisible">Visibility<br />
					<select size="1" name="isVisible" id="isVisible">
						<option value="">:: Choose ::</option>
						<?=$isVisibleOptions;?>
					</select>
				</label>				
				<label class="small left" for="sectionID">Site Section<br />
					<select size="1" name="sectionID" id="sectionID">
						<option value="">:: Choose ::</option>
						<?=$sections;?>
					</select>
				</label>
				<label class="small left" for="siteTemplateID">Page Template<br />
					<select size="1" name="siteTemplateID">
						<option value="">:: Choose ::</option>						
						<?=$siteTemplates;?>
					</select>
				</label>
				<label class="small left" for="publishingLayoutID">Page Layout<br />
					<select size="1" name="publishingLayoutID">
						<option value="">:: Choose ::</option>
						<?=$publishingLayouts;?>
						<option value="0">Blank (no layout)</option>						
					</select>
				</label>
			</fieldset>

			<fieldset class="clear">
				<legend>Authorization Settings</legend>
				<label class="small left" for="visitorAuthLevel">Viewer Authorization<br />
					<select size="1" name="visitorAuthLevel" id="visitorAuthLevel">
						<option value="">:: Choose ::</option>
						<?=$visitorAuthOptions;?>
					</select>
				</label>
				<label class="small left" for="editorAuthLevel">Editor Authorization<br />
					<select size="1" name="editorAuthLevel" id="editorAuthLevel">
						<option value="">:: Choose ::</option>
						<?=$editorAuthOptions;?>
					</select>
				</label>
				<input type="hidden" name="contentRecordNum" id="contentRecordNum" value="<?=$contentRecordNum;?>" />
			</fieldset>
			