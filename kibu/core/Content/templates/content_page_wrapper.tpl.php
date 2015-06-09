
			<div id="content">
				<section id="body">
				<?php
					if(count($localContent) > 0) {
						foreach($localContent as $key => $asset) {
							echo $asset;
						}
					}
					else {
						echo "";
					}
				?>
				</section>
				<section id="sidebar">
				<?php
					if(count($globalContent) > 0) {
						foreach($globalContent as $key => $globalAsset) {
							echo $globalAsset;
						}
					}
					else {
						echo "";
					}
				?>					
				</section>
			</div>
			<br class="clear" />