<?php

	echo '
	<div class="user-forms">
		<div class="utools-head">
			<div><span>'.$GD->text(43).'</span></div>
		</div>

		<div class="utools-body">
			<form>
			 	'.$GD->generate_inputs(100).'

				<div class="subs-send ss-s al">
					<div>
						<button type="button" class="ss-send" id="acc-login">'.$GD->text(49).'</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	';
	/*
	echo '

	<div class="user-forms">
		<div class="utools-head">
			<div><span>'.$GD->text(43).'</span></div>
		</div>

		<div class="utools-body">
			<div class="subs-input">
				<input type="text" class="ss-text" id="plogin-username">
				<label for="plogin-username"><span>'.$GD->text(56).'</span></label>
				<div class="ss-icon i i11"></div>
			</div>

			<div class="subs-input">
				<input type="password" class="ss-text" id="plogin-password">
				<label for="plogin-password"><span>'.$GD->text(57).'</span></label>
				<div class="ss-icon i i12"></div>
			</div>

			<div class="subs-send ss-s al">
				<div>
					<button type="button" class="ss-send" id="acc-login">'.$GD->text(49).'</button>
				</div>
			</div>

			<div class="login-forgot">
				<a href="'.$GD->link(11).'" class="l">'.$GD->text(50).'</a> '.$GD->text(51).'
			</div>
		</div>

		<div class="utools-footer">
			<div> '.$GD->text(44).'<a href="'.$GD->link(2).'" class="l"> '.$GD->text(45).'</a> <i>'.$GD->text(46).'</i></div>
		</div>
	 </div>
	';*/