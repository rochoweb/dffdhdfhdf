<?= '
	<div class="basketDetailed">
		<div class="bd-left">
			<div class="bd-content">
				<div class="order-check">
					<div class="och-content">
						<div class="och-map">
							<div class="gmap-content"><div class="gmap-map"><div class="gmap-default" id="gmap"></div></div></div>
						</div>

						'.$BA->generate_adresss().'

						<div class="ed"><a href="'.$GD->link(17).'#'.$GD->text(593).'" class="editData ls05"><i class="ii iRe" aria-hidden="true"></i>'.$GD->text(592).'</a></div>

						<div class="cleaner"></div>
					</div>
				</div>


				<div class="basket-data" id="basketF">

					<div class="basket-content" id="basketItems">
						'.$BA->basket_items(false).'

						<div class="ed"><a href="'.$GD->link(17).'" class="editData ls05"><i class="ii iRe" aria-hidden="true"></i>'.$GD->text(595).'</a></div>
					</div>

					<div class="backdoor">
						<a href="'.$GD->link(17).'" class="om"><span class="ii iLeft2"></span>'.$GD->text(811).'</a>
					</div>
				</div>
			</div>
		</div>

		<div class="bd-right">
			<div class="bd-content">
				<div class="cart">
					<div class="cart-summary">
						<div class="cart-head"><span>'.$GD->text(521).'</span></div>
						<div class="cart-summ">
							'.$BA->cart_summary().'
						</div>
					</div>

					<div class="cart-buttons">
						<div class="sendbb">
							<p class="sd-info">'.sprintf($GD->text(823), '<strong>'.$GD->text(585).'</strong>', '<a href="'.$GD->link(501).'">'.$GD->text(824).'</a>').'</p>
							<a href="#" class="cart-continue contactive" id="sendorder">'.$GD->text(585).' <span>'.$GD->text(822).'</span><div class="cart-load"><i class="fa fa-cog fa-spin"></i></div></a>
							<p class="sd-mob">'.$GD->text(825).'</p>

							<div class="sd-return"><a href="'.$GD->link(17).'" class="om"><span class="ii iLeft2"></span>'.$GD->text(811).'</a></div>
						</div>

						<p class="result"></p>

						
					</div>
				</div>
			</div>
		</div>
		
		<div class="cleaner"></div>
	</div>
	';
?>