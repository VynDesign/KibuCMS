				
					<fieldset>
						<legend><?=$legend;?></legend>
						<label class="left twothirds" for="permRoleName">Role Display Name<br />
							<input class="full" type="text" id="permRoleName" name="permRoleName" value="<?=$permRoleName;?>" />
						</label>
						<label class="left third" for="permRoleAbbrv">Role Abbrv.<br />
							<input class="full" type="text" id="permRoleAbbrv" name="permRoleAbbrv" value="<?=$permRoleAbbrv;?>"/>
						</label>
						<br class="clear" />
						<label for="permRoleDesc">Role Description<br />
							<input type="text" class="full" id="permRoleDesc" name="permRoleDesc" value="<?=$permRoleDesc;?>" />
						</label>
						<input type="hidden" id="permRoleGUID" name="permRoleGUID" value="<?=$permRoleGUID;?>" />
					</fieldset>

					<?if($assignedAbilities != null):?>
					<fieldset>
						<legend>Assigned Abilities</legend>
						<p class="small">Below are all abilities that <strong>HAVE</strong> been assigned to this role. Check the box to the left of each to <strong>REMOVE</strong> these abilities when the form is processed.</p>
						<?foreach($assignedAbilities as $abilityGuid => $abilityName):?>
						<label class="left third" for="removeAbilities[<?=$abilityGuid;?>]">
							<input type="checkbox" id="removeAbilities[<?=$abilityGuid;?>]" name="removeAbilities[<?=$abilityGuid;?>]" /> <?=$abilityName;?>
						</label>
						<?endforeach;?>
					</fieldset>
					<?endif;?>

					<?if($unassignedAbilities != null):?>
					<fieldset>
						<legend>Unassigned Abilities</legend>
						<p class="small">Below are all abilities that have <strong>NOT</strong> been assigned to this role. Check the box to the left of each to <strong>ADD</strong> these abilities when the form is processed.</p>						
						<?foreach($unassignedAbilities as $abilityGuid => $abilityName):?>
						<label class="left third" for="addAbilities[<?=$abilityGuid;?>]">
							<input type="checkbox" id="addAbilities[<?=$abilityGuid;?>]" name="addAbilities[<?=$abilityGuid;?>]" /> <?=$abilityName;?>
						</label>
						<?endforeach;?>
					</fieldset>
					<?endif;?>