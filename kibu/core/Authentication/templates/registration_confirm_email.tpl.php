Hello, <?=$userName;?>. You are receiving this email because you recently filled out a <?=$formType;?> form at <?=$site;?>. Your new account information is below.

Login Information:
Username: <?=$userName."\n";?>
Password: <?=$password."\n";?>
<?if($formType == "registration"):?>
Authorization Code: <?=$authCode."\n";?>

Save this email, write down, or otherwise ensure your login information is accessible, as your password is saved in our database in an encrypted state, and cannot be retrieved in its original state.

The next time you log in at <?=$site;?>, you will be prompted to verify your email address. On the screen after clicking 'Verify your email' you will be asked to enter your email address and the Authorization Code shown above, with your Username and Password.
<?endif;?>