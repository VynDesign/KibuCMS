				
					<fieldset>
						<legend><?=$legend;?></legend>
						<label class="left twothirds"for="permAbilityName">Ability Display Name<br />
							<input class="full" type="text" id="permAbilityName" name="permAbilityName" value="<?=$permAbilityName;?>" />
						</label>
						<label class="right third" for="permAbilityAbbrv">Ability Abbrv.<br />
							<input class="full" type="text" id="permAbilityAbbrv" name="permAbilityAbbrv" value="<?=$permAbilityAbbrv;?>"/>
						</label>
						<label for="permAbilityDesc">Ability Description<br />
							<input type="text" class="full" id="permAbilityDesc" name="permAbilityDesc" value="<?=$permAbilityDesc;?>" />
						</label>
						<input type="hidden" id="permAbilityGUID" name="permAbilityGUID" value="<?=$permAbilityGUID;?>" />
					</fieldset>