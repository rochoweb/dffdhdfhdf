<?php
	
	echo '

	<div class="user-forms">
		<div class="utools-head">
			<div><span>'.$GD->text(42).'</span></div>
		</div>

		<div class="utools-body">
			<form>
				'.$GD->generate_inputs(503).'
				<div class="subs-send ss-s al">
					<div>
						<button type="button" class="ss-send l" id="acc-signup">'.$GD->text(55).'</button>
					</div>
				</div>
			</form>
		</div>

		<div class="utools-footer">
			<div> '.$GD->text(47).' <a href="'.$GD->link(3).'" class="fc-show l" name="login">'.$GD->text(48).'</a> </div>
		</div>
	</div>
	';