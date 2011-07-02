
						<h1><?=$pageTitle;?></h1>

						<p>
								The first thing we need to do is hook up to the database. As every web hosting company sets up MySQL slightly differently, creating
								the database needs to be done outside of this installer. Don't worry, we'll take care of all the really technical bits like creating
								tables and inserting data - you just need to create the empty database and make note of the User, Password, Host and Database Name and
								insert them into the form below. This will allow us to configure Kibu to read from and write to the database.
						</p>

						<fieldset>
								<legend>Database User Info</legend>
								<label class="half" for="dbUser">Database User<br />
										<input class="full" type="text" name="dbUser" id="dbUser" value="<?=$dbUser;?>" /><br />
										<span class="small">The username used to connect to the database</span>
								</label>
								<br />
								<label class="half left" for="dbPass">Database User Password<br />
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