<?php
	/*'.$BA->basket_shipping().'*/

	if ( $BA->basket )
		$delIcon = $BA->basket->delivery_firstname || $BA->basket->delivery_lastname || $BA->basket->delivery_phone  || $BA->basket->delivery_street  || $BA->basket->delivery_city  || $BA->basket->delivery_zip ? '<i class="ii iCheck"></i>' : '<i class="ii iUncheck"></i>';
	else
		$delIcon = "";
	
	if ( $BA->basket )
		$compIcon = $BA->basket->company_company || $BA->basket->company_cid || $BA->basket->company_tin  || $BA->basket->company_tax ? '<i class="ii iCheck"></i>' : '<i class="ii iUncheck"></i>';
	else
		$compIcon = "";


	echo '

	<div class="basketDetailed">
		<div class="bd-left">
			<div class="bd-content">
				
				
				<div class="basket-data" id="basketF">

					<div class="basket-head">
						<div class="basket-head-title">
							<a name="'.$GD->text(596).'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
							<div class="ribbon"><div class="rib-text"><span class="rib rib-left"><span></span></span><h1>'.$GD->text(826).'</h1><span class="rib rib-right"><span></span></span></div></div>
						</div>
					</div>
					<a name="doprava-a-platba"></a>
					<div class="basket-content" id="basketItems">
						'.$BA->basket_items(true).'

						'.$CHB.'
					</div>
				</div>

				<div class="basket-data" id="deliveryF">
					<div class="basket-head">
						<div class="basket-head-title">
							<a name="'.$GD->text(597).'"><i class="fa fa-cube" aria-hidden="true"></i></a>
							<div class="ribbon"><div class="rib-text"><span class="rib rib-left"><span></span></span><h2>'.$GD->text(827).'</h2><span class="rib rib-right"><span></span></span></div></div>
						</div>
					</div>

					<div class="basket-content">
						<div class="choice-left">
							<div class="choice-content">
								<div class="choice-head">
									<span>'.$GD->text(589).'</span>
								</div>

								<div class="choice-body" data-type="delivery-type">
									'.$BA->select_delivery_type().'
								</div>
							</div>
						</div>

						<div class="choice-right secondChoice">
							<div class="choice-content">
								<div class="choice-head">
									<span>'.$GD->text(588).'</span>
								</div>

								<div class="choice-body" data-type="delivery-payment">
									'.$BA->select_delivery_payment().'
								</div>
							</div>
						</div>

						<div class="cleaner"></div>
					</div>
				</div>

				<div class="basket-data" id="userdataF">
					<div class="basket-head">
						<div class="basket-head-title">
							<a name="'.$GD->text(593).'"><i class="fa fa-user" aria-hidden="true"></i></a>
							<div class="ribbon"><div class="rib-text"><span class="rib rib-left"><span></span></span><h2>'.$GD->text(549).'</h2><span class="rib rib-right"><span></span></span></div></div>
						</div>

						<div class="basket-head-info"><span>*</span>'.$GD->text(562).'</div>
					</div>

					<div class="basket-content">
						<div class="tabmenu scrollv">

								<a href="#tab1" id="tabA" class="m1">'.$GD->text(551).'</a><a href="#tab2" class="m2"><span class="tabmenuIcon deliverytm">'.$delIcon.'</span>'.$GD->text(576).'</a><a href="#tab3" class="m3"><span class="tabmenuIcon companytm">'.$compIcon.'</span>'.$GD->text(561).'</a>

							<div class="cleaner"></div>
						</div>

						<div id="basketform">
							<form name="billing">
								<div class="tab tA tab1">
									<div class="tab-head">'.$GD->text(568).'</div>
									<div class="choice-left">
										<div class="inpbox-content">
											'.$GD->generate_inputs(1, true, $BA).'
										</div>
									</div>

									<div class="choice-right">
										<div class="inpbox-content">
											'.$GD->generate_inputs(2, true, $BA).'
											
											<div class="tabInf"><a href="#tab2" class="tabSwitcher" data-menu="tabmenu" data-target="m2">'.$GD->text(567).'</a></div>
										</div>
									</div>

									<div class="cleaner"></div>
								</form>
							</div>

							<div class="tab tab2">
								<form name="delivery">
									<div class="tab-head"><p>'.$GD->text(559).'</p><i>'.$GD->text(773).'</i></div>
									<div class="choice-left">
										<div class="inpbox-content">
											'.$GD->generate_inputs(3, true, $BA).'
											
										</div>
									</div>

									<div class="choice-right">
										<div class="inpbox-content">
											'.$GD->generate_inputs(4, true, $BA).'
											
										</div>
									</div>

									<div class="cleaner"></div>
								</form>
							</div>

							<div class="tab tab3">
								<form name="company">
									<div class="tab-head"><p>'.$GD->text(566).'</p><i>'.$GD->text(773).'</i></div>

									<div class="choice-left">
										<div class="inpbox-content">
											'.$GD->generate_inputs(5, true, $BA).'
											
										</div>
									</div>

									<div class="choice-right">
										<div class="inpbox-content">
											'.$GD->generate_inputs(6, true, $BA).'
										</div>
									</div>

									<div class="cleaner"></div>
								</form>
							</div>

						</div>
					</div>
				</div>

				<div class="backdoor">
					<a href="'.$GD->link(1).'" class="om"><span class="ii iLeft2"></span>'.$GD->text(598).'</a>
				</div>
			</div>
		</div>

		<div class="bd-right">
			<div class="bd-content">
				<div class="cart">
					<div class="cart-summary">
						<div class="cart-head"><span><h2>'.$GD->text(521).'</h2></span></div>
						<div class="cart-summ">
							'.$BA->cart_summary().'
						</div>
					</div>

					<div class="cart-buttons">
						<a href="#" class="cart-continue contactive" id="basketcontinue">'.$GD->text(524).'<div class="cart-load"><i class="fa fa-cog fa-spin"></i></div></a>
						<p class="result"></p>
						
						<a href="'.$GD->link(1).'" class="om"><span class="ii iLeft2"></span>'.$GD->text(598).'</a>
					</div>
				</div>
			</div>
		</div>
		
		<div class="cleaner"></div>
	</div>

	'.$GI->gen_itemhistory().'
	';
?>