<?= '
			<div class="about">
				
				<div class="about-left">
					<div class="bfm-move bfmleft"><span class="f-up ii iLeft"></span></div>
					<div class="scrollv">
						
						<div class="aboutleft">
							'.$GD->about_menu(1).'

							'.$GD->about_menu(2).'

							'.$GD->about_menu(3).'
							<div class="cleaner"></div>
						</div>
						
						<div class="aboutleftMobile">
							'.$GD->about_menu(1, false).''.$GD->about_menu(2, false).''.$GD->about_menu(3, false).'
							<div class="cleaner"></div>
						</div>

					</div>
					<div class="bfm-move bfmright"><span class="f-up ii iRight"></span></div>
				</div>
				<div class="about-right">
					<div class="aboutright">
						'.$GD->about_content().'
					</div>
				</div>

				<div class="cleaner"></div>
			</div>

	'.$GI->gen_itemhistory().'
';