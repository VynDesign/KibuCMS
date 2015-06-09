

								<script type="text/javascript">
									$(document).ready(function() {
										$("a#login").fancybox({ hideOnOverlayClick: false, height: 300, width: 550, type: 'iframe' });
										$("a#register").fancybox({ hideOnOverlayClick: false, height: 450, width: 700, type: 'iframe' });
									});
								</script>
								<a id="login" href="/modal.php?dir=Authentication&amp;class=LoginLogout&amp;mode=login&amp;curPage=<?=$curPage;?>" title="Login">Login</a> | <a id="register" href="/modal.php?dir=Authentication&amp;class=Registration&amp;mode=register&amp;curPage=<?=$curPage;?>" title="Register">Register</a>
