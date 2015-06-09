
								<script type="text/javascript">
									$(document).ready(function() {
										$("a#logout").fancybox({hideOnOverlayClick: false, height: 175, width: 350, type: 'iframe'});
										$("a#changePW").fancybox({hideOnOverlayclick: false, height:300, width: 350, type: 'iframe'});
									});
								</script>
								Logged in as <?=$userName;?>. <a id="logout" href="/modal.php?dir=Authentication&amp;class=LoginLogout&amp;mode=logout&amp;curPage=<?=$curPage;?>">Logout</a> | <a id="changePW" href="/modal.php?dir=Authentication&amp;class=Registration&amp;mode=changepassword&amp;curPage=<?=$curPage;?>" title="Change Password">Change Password</a>