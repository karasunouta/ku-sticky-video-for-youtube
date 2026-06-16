import './ku-sticky-video-for-youtube-admin.css';

document.addEventListener( 'DOMContentLoaded', function () {
	// Targeting mode radios
	const radios = document.querySelectorAll( '.targeting-mode-radio' );
	if ( radios.length ) {
		const updateTargetingFields = function () {
			const checkedRadio = document.querySelector(
				'.targeting-mode-radio:checked'
			);
			const selectedMode = checkedRadio ? checkedRadio.value : 'exclude';

			const excludeFields =
				document.querySelectorAll( '.exclude-fields' );
			const includeFields =
				document.querySelectorAll( '.include-fields' );
			const excludeInstructions = document.querySelectorAll(
				'.exclude-instruction'
			);
			const includeInstructions = document.querySelectorAll(
				'.include-instruction'
			);

			if ( selectedMode === 'exclude' ) {
				excludeFields.forEach( function ( el ) {
					el.style.display = '';
				} );
				excludeInstructions.forEach( function ( el ) {
					el.style.display = '';
				} );
				includeFields.forEach( function ( el ) {
					el.style.display = 'none';
				} );
				includeInstructions.forEach( function ( el ) {
					el.style.display = 'none';
				} );
			} else {
				excludeFields.forEach( function ( el ) {
					el.style.display = 'none';
				} );
				excludeInstructions.forEach( function ( el ) {
					el.style.display = 'none';
				} );
				includeFields.forEach( function ( el ) {
					el.style.display = '';
				} );
				includeInstructions.forEach( function ( el ) {
					el.style.display = '';
				} );
			}
		};

		radios.forEach( function ( radio ) {
			radio.addEventListener( 'change', updateTargetingFields );
		} );

		// Run initially
		updateTargetingFields();
	}

	// Width settings unit selection toggle
	const widthUnitSelect = document.getElementById( 'sticky_width_unit' );
	if ( widthUnitSelect ) {
		const pxContainer = document.getElementById(
			'width_input_px_container'
		);
		const pctContainer = document.getElementById(
			'width_input_pct_container'
		);
		const maxSettingsContainer = document.getElementById(
			'width_max_settings_container'
		);
		const maxOriginalCheckbox = document.getElementById(
			'sticky_width_max_original'
		);
		const maxCustomActiveCheckbox = document.getElementById(
			'sticky_width_max_custom_active'
		);
		const maxCustomValInput = document.getElementById(
			'sticky_width_max_custom_val'
		);

		const updateWidthFields = function () {
			const isPx = widthUnitSelect.value === 'px';

			if ( isPx ) {
				if ( pxContainer ) pxContainer.style.display = 'inline-flex';
				if ( pctContainer ) pctContainer.style.display = 'none';

				if ( maxSettingsContainer ) {
					maxSettingsContainer.style.opacity = '0.5';
					maxSettingsContainer.style.pointerEvents = 'none';
				}
				if ( maxOriginalCheckbox ) maxOriginalCheckbox.disabled = true;
				if ( maxCustomActiveCheckbox ) maxCustomActiveCheckbox.disabled = true;
				if ( maxCustomValInput ) maxCustomValInput.disabled = true;
			} else {
				if ( pxContainer ) pxContainer.style.display = 'none';
				if ( pctContainer ) pctContainer.style.display = 'inline-flex';

				if ( maxSettingsContainer ) {
					maxSettingsContainer.style.opacity = '1';
					maxSettingsContainer.style.pointerEvents = 'auto';
				}
				if ( maxOriginalCheckbox ) maxOriginalCheckbox.disabled = false;
				if ( maxCustomActiveCheckbox ) maxCustomActiveCheckbox.disabled = false;
				if ( maxCustomValInput ) {
					maxCustomValInput.disabled = ! ( maxCustomActiveCheckbox && maxCustomActiveCheckbox.checked );
				}
			}
		};

		widthUnitSelect.addEventListener( 'change', updateWidthFields );

		// Run initially
		updateWidthFields();
	}

	// Custom max width checkbox toggle disabled input state
	const maxCustomActiveCheckbox = document.getElementById(
		'sticky_width_max_custom_active'
	);
	const maxCustomValInput = document.getElementById(
		'sticky_width_max_custom_val'
	);
	const widthUnitSelectForCustom = document.getElementById( 'sticky_width_unit' );
	if ( maxCustomActiveCheckbox && maxCustomValInput ) {
		const updateMaxCustomInput = function () {
			if ( widthUnitSelectForCustom && widthUnitSelectForCustom.value === 'px' ) {
				maxCustomValInput.disabled = true;
			} else {
				maxCustomValInput.disabled = ! maxCustomActiveCheckbox.checked;
			}
		};
		maxCustomActiveCheckbox.addEventListener(
			'change',
			updateMaxCustomInput
		);

		// Run initially
		updateMaxCustomInput();
	}

	// Top exclusion zone active checkbox toggle disabled state
	const limitTopActiveCheckbox = document.getElementById(
		'sticky_limit_top_active'
	);
	const limitTopValInput = document.getElementById( 'sticky_limit_top_val' );
	if ( limitTopActiveCheckbox && limitTopValInput ) {
		const updateLimitTopInput = function () {
			limitTopValInput.disabled = ! limitTopActiveCheckbox.checked;
		};
		limitTopActiveCheckbox.addEventListener(
			'change',
			updateLimitTopInput
		);

		// Run initially
		updateLimitTopInput();
	}

	// Bottom exclusion zone active checkbox toggle disabled state
	const limitBottomActiveCheckbox = document.getElementById(
		'sticky_limit_bottom_active'
	);
	const limitBottomValInput = document.getElementById(
		'sticky_limit_bottom_val'
	);
	if ( limitBottomActiveCheckbox && limitBottomValInput ) {
		const updateLimitBottomInput = function () {
			limitBottomValInput.disabled = ! limitBottomActiveCheckbox.checked;
		};
		limitBottomActiveCheckbox.addEventListener(
			'change',
			updateLimitBottomInput
		);

		// Run initially
		updateLimitBottomInput();
	}

	// Mobile breakpoint active checkbox toggle disabled state
	const mobileBreakpointActiveCheckbox = document.getElementById(
		'sticky_mobile_breakpoint_active'
	);
	const mobileBreakpointValInput = document.getElementById(
		'sticky_mobile_breakpoint_val'
	);
	if ( mobileBreakpointActiveCheckbox && mobileBreakpointValInput ) {
		const updateMobileBreakpointInput = function () {
			mobileBreakpointValInput.disabled =
				! mobileBreakpointActiveCheckbox.checked;
		};
		mobileBreakpointActiveCheckbox.addEventListener(
			'change',
			updateMobileBreakpointInput
		);

		// Run initially
		updateMobileBreakpointInput();
	}
} );
