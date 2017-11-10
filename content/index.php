
						<?= '
							
							<div class="hello-index">
								<h1 class="nv">'.$GD->text(874).'</h1>

								'.$R["diy"].'
								<div class="cleaner"></div>
							</div>

							'.$R["list"].'

							<div class="home-double">
								<div class="hod hod-left">
									<div class="hod-content">
										'.$GD->gen_seasons().'
									</div>
								</div>

								<div class="hod hod-right">
									<div class="hod-content">
										'.$GD->gen_unavailable().'
									</div>
								</div>

								<div class="cleaner"></div>
							</div>

							'.$GI->gen_itemhistory().'
						';

