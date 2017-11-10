<?php
	
	echo '
		<div class="page">
			<div class="page-header">
				<div class="ph">
					<span class="ph-center">
						<h1>'.strtoupper( $GD->text(124) ).'</h1>
					</span>
				</div>
			</div>

			<div class="page-body">
				'.$TAGS->list_of_tags().'
			</div>
		</div>
		';