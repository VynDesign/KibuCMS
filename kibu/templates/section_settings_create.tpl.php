
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
				<input type="hidden" name="sectionNum" id="sectionNum" value="<?=$sectionNum;?>" />
