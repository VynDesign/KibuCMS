

								<fieldset>
										<legend>Choose a Template</legend>
										<label for="templateID">
												<select name="templateID" id="templateID">
														<option>.:Choose:.</option>
														<?foreach($template as $templateID => $templateName):?>
														<option value="<?=$templateID;?>"><?=$templateName;?></option>
														<?endforeach;?>
												</select>
										</label>
								</fieldset>
