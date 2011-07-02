						
						<fieldset>
								<legend>Database User Information</legend>
								<label class="half" for="dbUser">Database Username<br />
										<input class="full" type="text" name="dbUser" id="dbUser" value="<?=$dbUser;?>" /><br />
										<span class="small">The username used to connect to the database</span>
								</label>
								<br />
								<label class="half left" for="dbPass">Database Password<br />
										<input class="full" type="password" name="dbPass[]" id="dbPass[]" /><br />
										<span class="small">The password associated with that username</span>
								</label>
								<label class="half right" for="dbPass">Re-type Password<br />
										<input class="full" type="password" name="dbPass[]" id="dbPass[]" /><br />
										<span class="small">In order to catch any misspelling before next step</span>
								</label>
						</fieldset>
						<fieldset>
								<legend>Database Server Information</legend>
								<label class="half left" for="dbHost">Database Host<br />
										<input class="full" type="text" name="dbHost" id="dbHost" value="<?=$dbHost;?>" /><br />
										<span class="small">The domain where the database is housed (usually 'localhost' but can be different)</span>
								</label>
								<label class="half right" for="dbPass">Database Name<br />
										<input class="full" type="text" name="dbName" id="dbName" value="<?=$dbName;?>" /><br />
										<span class="small">Name of the database to connect to for this installation</span>
								</label>
						</fieldset>