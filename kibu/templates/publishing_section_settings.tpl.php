			<fieldset class="left twothirds">
				<legend>Section Display Name</legend>
				<label for="sectionFullName">
					<input type="text" class="full" name="sectionFullName" id="sectionFullName" value="<?=$sectionFullName;?>" />
					Name of this section displayed in navigation and title bar
				</label>
			</fieldset>
			<fieldset class="right third">
				<legend>Section URL Name</legend>
				<label for="sectionName">
					<input type="text" class="full" name="sectionName" id="sectionName" value="<?=$sectionName;?>" />
                                        Determined by section name if left blank
				</label>
			</fieldset>
			<fieldset class="clear">
				<legend>Publishing Controls</legend>
				<label>Options for publishing this section.</label>
				<label class="small left" for="isSiteDefault">Site Default?<br />
					<select size="1" name="isSiteDefault">
						<option value="">:: Choose ::</option>
						<option value="y"<?php if ($isSiteDefault == 'y') {echo " selected=\"selected\"";} ?>>Yes</option>
						<option value="n"<?php if ($isSiteDefault == 'n') {echo " selected=\"selected\"";} ?>>No</option>
					</select>
				</label>
				<label class="small left" for="sectionVisible">
					<select size="1" name="sectionVisible" id="sectionVisible">
						<option value="">:: Choose ::</option>
						<option value="vis"<?php if ($isVisible == 'vis') {echo " selected=\"selected\"";} ?>>Visible</option>
						<option value="invis"<?php if ($isVisible == 'invis') {echo " selected=\"selected\"";} ?>>Invisible but Accessible</option>
                                                <option value="inac"<?php if($isVisible =='inac') {echo " selected=\"selected\"";}?>>Inaccessible</option>
					</select>
				</label>
				<label class="small left" for="visitorAuthLevel">Viewer Authorization<br />
					<select size="1" name="visitorAuthLevel" id="visitorAuthLevel">
						<option value="">:: Choose ::</option>
						<?foreach($userLevels as $visitorLevel):?>
						<option value="<?=$visitorLevel['levelNum'];?>"<?php if ($visitorAuthLevel == $visitorLevel['levelNum']) {echo " selected=\"selected\"";} ?>><?=$visitorLevel['levelName'];?></option>
						<?endforeach;?>
					</select>
				</label>
			</fieldset>