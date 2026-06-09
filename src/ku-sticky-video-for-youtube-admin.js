import './ku-sticky-video-for-youtube-admin.css';

document.addEventListener( 'DOMContentLoaded', function () {
	const radios = document.querySelectorAll( '.targeting-mode-radio' );
	if ( ! radios.length ) {
		return;
	}

	function updateTargetingFields() {
		const checkedRadio = document.querySelector( '.targeting-mode-radio:checked' );
		const selectedMode = checkedRadio ? checkedRadio.value : 'exclude';

		const excludeFields = document.querySelectorAll( '.exclude-fields' );
		const includeFields = document.querySelectorAll( '.include-fields' );
		const excludeInstructions = document.querySelectorAll( '.exclude-instruction' );
		const includeInstructions = document.querySelectorAll( '.include-instruction' );

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
	}

	radios.forEach( function ( radio ) {
		radio.addEventListener( 'change', updateTargetingFields );
	} );

	// Run initially
	updateTargetingFields();
} );
