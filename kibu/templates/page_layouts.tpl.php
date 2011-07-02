
		<?=$javaScript;?>
		<fieldset>
				<legend>Page Layouts</legend>
				<label for="pageLayout" class="half left" style="float:left;">Choose a page layout<br />
					<select name="pageLayout" id="pageLayout" class="full" size="5" onChange="replaceText('layoutDescription',this.value);">
							<option value="0">.: Choose :.</option>
							<?=$layoutOptions;?>
					</select>
				</label>
				<label class="half right">
					Page Layout Description:<br />
					<span class="descriptionbox" id="layoutDescription">Select a page layout name to the left to view its description here.</span>
				</label>
				<input type="hidden" name="contentRecordNum" id="contentRecordNum" value="<?=$contentRecordNum;?>" />
			</fieldset>