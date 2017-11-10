<?php
	/*switch ( $IP->IP->availability ) {
		case 1:
			$class = " availOK";
			break;
		case 8:
			$class = " availNOT";
			break;
		default:
			$class = " availWORK";
			break;
	}*/

	echo '
	<div class="itemProfile" itemscope itemtype="http://schema.org/Product">
		<span class="dn" itemprop="brand">MONAMADE</span>

		<div class="ip-main">
			<div class="ip-left">
				<div class="ipl-content">
					<div class="ipr-head">
						<div class="ipr-head-main">
							<h1 itemprop="name">'.$GD->mb_ucfirst($IP->IP->title).'</h1>
							'.$IP->generate_breadcrumb().'
						</div>
						
						'.$IP->generate_promo().'
					</div>

					

					'.$IP->generate_galery().'
					'.$IP->generate_tags().'
				</div>
			</div>
			<div class="ip-right">
				<div class="ipr-content">
					

					<div class="ipr-shop">
						<div class="ipr-data'.$GD->gen_availability( $IP->IP->availability ).'">
							<div class="iprd data-prices" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
								<meta itemprop="priceCurrency" content="EUR" />
								<div class="ipr-prices">
									'.$IP->generate_price().'

									<div class="cleaner"></div>
								</div>

								'.$IP->generate_availability().'
							</div>	

							<div class="iprd data-basket">
								'.$GD->generate_addtobasket($IP->IP, $BA->basket).'
							</div>

							<div class="cleaner"></div>
						</div>

						<div class="ipr-info">
							'.$IP->generate_info().'

							'.$IP->generate_shareButtons().'
						</div>

						<div class="ipr-desc">
							'.$IP->generate_descriptions(true).'
						</div>
					</div>
				</div>
			</div>

			<div class="cleaner"></div>

			<div class="ip-bottom">
				<div class="ipr-desc">
					'.$IP->generate_descriptions().'
				</div>
			</div>
		</div>

		'.$IP->gen_itemrelated().'

		'.$GI->gen_itemhistory().'

	</div>';




