
				<div id="feedback" class="error message">
					<h3>One or more errors occurred:</h3>
					<ul>
						<?foreach($msg as $error):?>
						<li><?=$error;?></li>
						<?endforeach;?>
					</ul>
				</div>