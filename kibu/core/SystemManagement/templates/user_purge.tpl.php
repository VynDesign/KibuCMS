
					<fieldset>
						<legend>Purge Unverified Users</legend>
						<label class="half left" for="purgeStartDate">
							Purge Start Date<br />
							<select id="purgeStartDate[M]" name="purgeStartDate[M]">
									<?foreach($monthOpts as $key => $value):?>
									<option <?if($purgeStartDateM == $key):?>selected="selected"<?endif;?> value="<?=$key;?>"><?=$value;?></option>
									<?endforeach;?>
							</select>
							<select id="purgeStartDate[D]" name="purgeStartDate[D]">
									<?foreach($dayOpts as $key => $value):?>
									<option <?if($purgeStartDateD == $key):?>selected="selected"<?endif;?> value="<?=$key;?>"><?=$value;?></option>
									<?endforeach;?>
							</select>
							<select id="purgeStartDate[Y]" name="purgeStartDate[Y]">
									<?foreach($yearOpts as $key => $value):?>
									<option <?if($purgeStartDateY == $key):?>selected="selected"<?endif;?> value="<?=$key;?>"><?=$value;?></option>
									<?endforeach;?>
							</select>
						</label>
						
						<label class="half right" for="purgeEndDate">
							Purge End Date<br />
							<select id="purgeEndDate[M]" name="purgeEndDate[M]">
									<?foreach($monthOpts as $key => $value):?>
									<option <?if($purgeEndDateM == $key):?>selected="selected"<?endif;?> value="<?=$key;?>"><?=$value;?></option>
									<?endforeach;?>
							</select>
							<select id="purgeEndDate[D]" name="purgeEndDate[D]">
									<?foreach($dayOpts as $key => $value):?>
									<option <?if($purgeEndDateD == $key):?>selected="selected"<?endif;?> value="<?=$key;?>"><?=$value;?></option>
									<?endforeach;?>
							</select>
							<select id="purgeEndDate[Y]" name="purgeEndDate[Y]">
									<?foreach($yearOpts as $key => $value):?>
									<option <?if($purgeEndDateY == $key):?>selected="selected"<?endif;?> value="<?=$key;?>"><?=$value;?></option>
									<?endforeach;?>
							</select>
						</label>						
					</fieldset>
			