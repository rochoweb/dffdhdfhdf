<?php 
	$SHOPPAGE = $SHOP->generate_owneritems();

	if ( $SHOPPAGE["count"] == 0 )
		$newshopHead = array( 's' => $GD->text(211), 'p' => $GD->text(212) );
	else
		$newshopHead = array( 's' => $GD->text(213), 'p' => $GD->text( $SHOPPAGE["head"] ) );
?>
<div id="makesomething-line" class="re">
	<div class="newshop">
		<div class="newshop-top"> </div>

		<div class="newshop-content">
			<div class="container" id="page-editor">
				<div class="progress-bar">
					<div class="pb-line">
						<div class="pb-links">
							<?= $SHOP->shop_menu( $MM->pageid ); ?>
						</div>
					</div>

				</div>
				<div class="newshop-steps">
					
					<div class="newshop-step">
						<div class="newshop-step-header">
							
							<?= '<h1><strong>'.$newshopHead["s"].'</strong></h1>
									<p>'.$newshopHead["p"].'</p>'; ?>
						</div>
						<div class="newshop-step-menu">
							<div class="nsm-content">
								<?= $SHOPPAGE["menu"] ?>
							</div>

							<div class="cleaner"></div>
						</div>
						<div class="newshop-step-body">
						<?= $SHOPPAGE["list"]; ?>
							<?php

							
							/*echo '<form class="sfsdfds user-forms">

										<div class="subs-input">
											<input type="text" class="ss-text" id="shop-shopname">
											<label for="signup-username"><span>'.$GD->text(193).'</span></label>
											<div class="ss-icon i i11"></div>
											
											<div class="resultbar"></div>
										</div>

										<div class="subs-send ss-s al">
											<div>
												<button type="button" class="ss-send" id="shop-create">'.$GD->text(197).'</button>
											</div>
										</div>
								</form>';*/

								?>

							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
