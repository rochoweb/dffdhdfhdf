<?php
	if ( $HT ) {
		echo '
			<div class="page">
				<div class="page-header">
					<div class="ph">
						<span class="ph-center">
							<h1><span class="ii iTags"></span> <a href="'.$GD->link_a($_SERVER["REQUEST_URI"]).'" class="l" title="'.sprintf( $GD->text(117), ucfirst($GD->text($HT["id_text"])) ).'">'.strtoupper( $GD->text($HT["id_text"]) ).'</a> </h1>
						</span>
					</div>
				</div>


				<div class="page-body">
					<div class="count-results">
						<p>'.$GD->text(121).': <span>'.$R["count"].'</span></p>
					</div>

					<div class="found">
						'.$R["diy"].'

						<div class="cleaner"></div>
					</div>
					
					'.$R["list"].'
				</div>
			</div>
			';
	}
	else {
		echo '
		<div class="page">
			<div class="page-header">
				<div class="ph">
					<span class="ph-center">
						<h1># <a href="'.$GD->link_a($_SERVER["REQUEST_URI"]).'">'.$_GET["t"].'</a> </h1>
					</span>
				</div>
			</div>


			<div class="page-body">
				<div class="no-hashtag">
					<p>'.sprintf( $GD->text(119), ucfirst($_GET["t"]) ).'</p>
					<div> - <a href="'.$GD->link(7).'" class="l"> '.$GD->text(120).' </a> - </div>
				</div>
			</div>
		</div>
		';
	}
	