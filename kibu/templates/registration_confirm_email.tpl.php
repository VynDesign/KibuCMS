Hello, <?=$userName;?>. You are receiving this email because you recently filled out a <?=$formType;?> form at <?=$site;?>. Your new account information is below.

Login Information:
Username: <?=$userName."\n";?>
Password: <?=$password."\n";?>
<?if($formType == "registration"):?>
Authorization Code: <?=$authCode."\n";?>

Save this email, write down, or otherwise ensure your login information is accessible, as your password is saved in our database in an encrypted state, and cannot be retreived in its original state.

To activate your account, please confirm your email address by navigating back to <?=$site;?> and clicking the "Confirm Regisration" link on any page. You will be prompted to enter your email address and the Authorization Code above.
<?endif;?>