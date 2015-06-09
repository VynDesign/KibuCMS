
				<fieldset>
						<legend>Login</legend>
						<label for="userName" class="half left">Your Username<br />
								<input type="text" required="required" id="userName" name="userName" class="text" value="<?=$userName;?>" />
						</label>
						<label for="password" class="half right">Your password<br />
								<input type="password" required="required" id="password" name="password" class="text" />
						</label>
						<label>
							Forgot your login credentials? <a href="/modal.php?class=Registration&amp;mode=resetpassword&amp;curPage=" title="Reset Password">Reset your password</a>! Don't have an account? <a href="/modal.php?class=Registration&mode=register&curPage=" title="Register Now!">Register Now!</a>
						</label>
				</fieldset>
