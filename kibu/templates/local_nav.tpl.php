
						<div class="sidebarsection sideNav">
								<h4>Related Links</h4>
								<ul class="sideNav">
										<?foreach($localNode as $node):?>
										<li class="<?=$node['class'];?>">
												<a class="<?=$node['class']."link";?>" href="/<?=$node['sectionName'];?>/<?=$node['titleClean'];?>.html"><?=$node['contentTitle'];?></a>
										</li>
										<?endforeach;?>
								</ul>
						</div>