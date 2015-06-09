

			<fieldset>
				<legend>Edit User Details</legend>
				<label for="userName" class="third left">Username<br />
					<input type="text" class="full" name="userName" id="userName" value="<?=$userName;?>" />
				</label>
				<label for="emailAddress" class="third left">Email<br />
					<input type="text" class="full" name="emailAddress" id="emailAddress" value="<?=$emailAddress;?>" />
				</label>
				<label for="userLevel" class="third left">User Level<br />
					<select name="userLevel" id="userLevel">
						<option> - Select a User Level - </option>
						<?=$userLevelOpts;?>
					</select>					
				</label>
			</fieldset>
			<fieldset>
				<legend>Change Password</legend>
				<label>If you don't want to change this user's password, leave the below fields blank. Checking the box next to "Force Password Change?" will prompt the user to change their password to something else the next time they log in.<br /><br /></label>
				<label for="password[0]" class="third left">New Password<br />
					<input type="password" class="full" name="password[0]" id="password[0]" />
				</label>
				<label for="password[1]" class="third left">Re-enter New Password<br />
					<input type="password" class="full" name="password[1]" id="password[1]" />
				</label>				
				<label for="forcePWChange" class="small left"><br /><input type="checkbox" name="forcePWChange" <?if($forcePWChange == 1):?> checked="checked"<?endif;?> id="forcePWChange" /> Force Password Change?</label>
			</fieldset>
			<fieldset>	
				<legend>User Info</legend>
				<div class="half left">
					<span><strong>Join Date:</strong> <?=$joinDate;?></span><br />
					<span><strong>Verify Date:</strong> <?=$verifyDate;?></span><br />
					<span><strong>Last Active:</strong> <?=$lastActiveDate;?></span>
				</div>
				<div class="half right">
					<span><strong>Email Verified?:</strong> <?=$emailVerified;?></span><br />
					<span><strong>Initial IP:</strong> <?=$initIPAddr;?></span><br />
					<span><strong>Last IP:</strong> <?=$lastIPAddr;?></span>
				</div>
			</fieldset>