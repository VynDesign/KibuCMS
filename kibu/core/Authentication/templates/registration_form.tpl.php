
			 	<fieldset>
			 		<legend>Login Information</legend>
				 	<label for="userName" class="left third">
							Username<span class="message">*</span><br />
							<input class="full" required="required" type="text" id="userName" name="userName" value="<?=$userName;?>"/>
					</label>
			 		<label for="password[0]" class="left third">
							Password<span class="message">*</span><br />
							<input class="full" required="required" type="password" id="password[0]" name="password[0]" />
					</label>
			 		<label for="password[1]" class="left third">
							Re-type Password<span class="message">*</span><br />
							<input class="full" required="required" type="password" id="password[1]" name="password[1]" />
					</label>
			 	</fieldset>
				<fieldset class="reg clear">
					<legend>Email Address</legend>
					<label for="emailAddress[0]" class="half left">
							Enter Email Address<span class="message">*</span><br />
							<input class="full" required="required" type="text" id="emailAddress[0]" name="emailAddress[0]" value="<?=$emailAddress[0];?>"/>
					</label>
					<label for="emailAddress[1]" class="half left">
							Re-type Email Address<span class="message">*</span><br />
							<input class="full" required="required" type="text" id="emailAddress[1]" name="emailAddress[1]" value="<?=$emailAddress[1];?>"/>
					</label>
				</fieldset>
