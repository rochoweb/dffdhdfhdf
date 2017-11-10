<?php

		echo '
			<div class="categories">
				
				<div class="cat-top">
					<h1>'.mb_strtoupper($GD->text( $data->text ), "UTF8").'</h1>
					'.$GD->breadcrumb($CHR->PD->page, array($data, $R["count"] )).'
					<div class="cleaner"></div>
				</div>

				<div class="cat-left">
					<div class="catleft">
						<div class="filters">
							'.$filters[0].'
							
							'.$filters[1].'
										
							'.$filters[2].'

							'.$filters[3].'
						</div>
					</div>
				</div>
				
				<div class="cat-right">
					
					<div class="catright">
						'.$R["filters"].'

						<div class="found">
							'.$R["diy"].'

							<div class="cleaner"></div>
						</div>
					</div>
				</div>

				<div class="cleaner"></div>
			</div>

			'.$R["list"].'

			'.$GI->gen_itemhistory().'
			';
	