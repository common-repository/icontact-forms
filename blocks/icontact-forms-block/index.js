document.addEventListener('DOMContentLoaded',  function( ) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/#registering-a-block
	 */
	var registerBlockType = wp.blocks.registerBlockType;

	/**
	 * Returns a new element of given type. Element is an abstraction layer atop React.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/packages/packages-element/
	 */

	var el = wp.element.createElement;

	var useState = wp.element.useState;
	var useRef = wp.element.useRef;
	var useEffect = wp.element.useEffect;
	var useLayoutEffect = wp.element.useLayoutEffect;

	/**
	 * Retrieves the translation of text.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/packages/packages-i18n/
	 */
	var __ = wp.i18n.__;

	/**
	 *  Wordpress block props.
	 */
	var useBlockProps = wp.blockEditor.useBlockProps;

	/**
	 * Wodpress standard toolbar button.
	 */
	var ToolbarButton = wp.components.ToolbarButton;

	/**
	 * Wordpress block controls on gutenberg editor.
	 */
	var BlockControls = wp.blockEditor.BlockControls;	

	/**
	 * Create custom block icon.
	 */
	const icform_icon = el( 'svg', { width: 24, height: 24, viewBox: '0 0 43 44.4', style: { background: "new 0 0 43 44.4" }, x: "0px", y: "0px" },
		el( 'g', { transform: "translate(-2.003 -3.543)" }, 
		   [
			el( 
				'path', 
				{
					fill: '#135A9D',
					d: 'M32 19.4c-0.1 0.5-0.2 1-0.3 1.5c-0.1 0.4-0.4 0.7-0.7 1c-0.3 0.3-0.7 0.5-1.1 0.6c-0.6 0.1-1.2 0.2-1.7 0.3 h-1.6c-0.4 0-0.7-0.2-1-0.4c-0.3-0.2-0.4-0.6-0.4-0.9V20c0-0.5 0.1-0.9 0.3-1.4c0.2-0.4 0.4-0.7 0.7-1c0.3-0.3 0.7-0.5 1.1-0.6 c0.5-0.2 1.1-0.3 1.7-0.3h1.7c0.4 0 0.8 0.2 1 0.5c0.3 0.2 0.4 0.6 0.4 0.9C32.1 18.5 32 19 32 19.4 M27.6 46 c0 0.2-0.1 0.3-0.2 0.4c-0.1 0.2-0.3 0.3-0.5 0.3c-0.3 0.1-0.7 0.2-1 0.2C25.5 47 25 47 24.4 47.1h-1.5c-0.3 0-0.6 0-0.9-0.1 c-0.2 0-0.3-0.1-0.4-0.3c-0.1-0.1-0.1-0.3-0.1-0.4l2.8-21c0-0.2 0.1-0.3 0.2-0.4c0.1-0.2 0.3-0.3 0.5-0.3c0.3-0.1 0.6-0.2 1-0.3 c0.5-0.1 1-0.1 1.5-0.2H29c0.3 0 0.6 0 0.9 0.1c0.2 0 0.3 0.1 0.5 0.3c0.1 0.1 0.1 0.3 0.1 0.4L27.6 46z',
				}
			),
			el( 
				'path', 
				{
					fill: '#135A9D',
					d: 'M23.6 10.7c-0.2 0-0.4 0-0.6 0.1c-2.5 1.4-4.3 3.7-5.1 6.4c-0.7 2.6-0.3 5.4 1.1 7.7c0.2 0.3 0.6 0.6 1 0.6 c0.2 0 0.4 0 0.6-0.2c0.5-0.3 0.7-1 0.4-1.5c0 0 0 0 0-0.1c-1.1-1.8-1.4-3.9-0.8-5.9c0.6-2.1 2-3.9 3.9-4.9c0.6-0.3 0.8-1 0.5-1.6 c0 0 0 0 0 0C24.4 10.9 24 10.7 23.6 10.7',
				}
			),
			el( 
				'path', 
				{
					fill: '#135A9D',
					d: 'M16 25.3c-1.6-2.6-2-5.8-1.2-8.7c1-3.6 3.5-6.6 6.8-8.4c0.8-0.4 1.2-1.4 0.8-2.3C22 5.1 21 4.7 20.2 5.1 c-0.1 0-0.1 0.1-0.2 0.1c-4.1 2.3-7.2 6.1-8.5 10.6c-1 3.8-0.5 7.8 1.6 11.2c0.5 0.8 1.5 1 2.3 0.6C16.1 27.2 16.4 26.2 16 25.3 C16 25.4 16 25.3 16 25.3',
				}
			),		
			]
		)
	);

	function renderForm({formHTML}) {
		const ref = useRef();
		useLayoutEffect(() => {
			const range = document.createRange();
			range.selectNode(ref.current);
			ref.current.innerHTML = '';
			if (formHTML) {
				ref.current.append(range.createContextualFragment(formHTML));
			}
		}, [formHTML]);

		return el('div', {ref, id: 'icform-output', className: 'icform-output', dangerouslySetInnerHTML: {__html: formHTML}});
	}
	function getFirstFormId() {
		var first = document.querySelector('.wp-block-icontact-forms-icontact-forms-block[data-active-form="1"]');
		if (first) {
			first = first.getAttribute('id');
		}
		return first;
	}

	/**
	 * Every block starts by registering a new block type definition.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/#registering-a-block
	 */
	registerBlockType( 'icontact-forms/icontact-forms-block', {
		/**
		 * This is the display title for your block, which can be translated with `i18n` functions.
		 * The block inserter will show this name.
		 */
		title: __( 'iContact Forms', 'icontact-forms' ),

		/**
		 * An icon property should be specified to make it easier to identify a block.
		 * These can be any of WordPress Dashicons, or a custom svg element.
		 */
		//icon: 'email-alt2',
		icon: icform_icon,

		/**
		 * Blocks are grouped into categories to help users browse and discover them.
		 * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
		 */
		category: 'widgets',

		/**
		 * Optional block extended support features.
		 */
		supports: {
			// Removes support for an HTML mode.
			html: false,
		},
		attributes: {
			cid: {type: 'string'},
			cfid: {type: 'string'},
			formid: {type: 'string'},
			preview: {type: 'bool', default: 0},
		},

		/**
		 * The edit function describes the structure of your block in the context of the editor.
		 * This represents what the editor will render when the block is used.
		 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#edit
		 *
		 * @param {Object} [props] Properties passed from the editor.
		 * @return {Element}       Element to render.
		 */
		edit: function ( props ) {				
			var blockProps = useBlockProps();
			var formid = props.attributes.formid;
			var cfid = props.attributes.cfid;
			var cid = props.attributes.cid;
			var preview = props.attributes.preview;				 //To know if we are on a preview mode or not
			const [formHTML, setFormHTML] = useState(null);
			const [, setReRender] = useState(null);
			const [forms, setForms] = useState(icontact_forms_block_block_editor.forms); //Contain the forms index array data
			var first = getFirstFormId();

			useEffect(function () {
				if (formid) {
					var queryString = new URLSearchParams({
						formid: formid,
						cfid: cfid,
						cid: cid,
						preview: preview
					}).toString();
					let url = `${ajaxurl}?action=${icontact_forms_block_block_editor.prefix}form_output&_wpnonce=${icontact_forms_block_block_editor._wpnonce}&${queryString}`;
					fetch(url)
						.then((response) => response.text())
						.then(output => {
							setFormHTML(output);
						})
				} else {
					let url = `${ajaxurl}?action=${icontact_forms_block_block_editor.prefix}form_index&_wpnonce=${icontact_forms_block_block_editor._wpnonce}`;
					fetch(url)
						.then((response) => response.json())
						.then((forms_array) => {
							icontact_forms_block_block_editor.forms = forms_array.data;
							setForms(forms_array.data);
						});
				}
			}, [formid, preview]);

			var current_index = 0;
			var input_placeholder = 'Search form...';
			if ( forms.length == 0 ) {
				input_placeholder = 'No forms found';
			}
			
			/**
			 * Set the form ID selected from the search and exists preview mode
			 * 
			 * @param {*} event 
			 */
			function show_selected_form( event ) {				
				event.target.parentElement.classList.toggle( "show" );
				props.setAttributes( { formid: event.target.getAttribute( 'value' ), cfid: event.target.getAttribute( 'cfid' ), cid: event.target.getAttribute( 'cid' ) } );
				save_preview();
				event.preventDefault();
			}

			/**
			 * Filter search dropdown results
			 * 
			 * @param {*} event 
			 */
			function filter_form_list( event ) {
				var input, filter, ul, li, a, i;
				var icform_list = event.target.parentElement.children.namedItem( 'icforms-list' );
				icform_list.classList.add( 'show' );
				input = event.target;
				filter = input.value.toUpperCase();
				var div = icform_list;
				a = div.getElementsByTagName( "a" );
				for ( i = 0; i < a.length; i++ ) {
				  var txtValue = a[i].textContent || a[i].innerText;
				  if ( txtValue.toUpperCase().indexOf( filter ) > -1 ) {
					a[i].parentElement.style.display = "";
				  } else {
					a[i].parentElement.style.display = "none";
				  }
				}
			}
			
			/**
			 * Show the dropdown menu list.
			 * 
			 * @param {*} event 
			 */
			function show_list( event ) {
				var form_list = event.target.parentElement.children.namedItem( 'icforms-list' );
				form_list.classList.toggle( "show" );
			}

			/**
			 * Set preview true to show the form preview.
			 */
			function show_preview() {
				setFormHTML(null);
				props.setAttributes( { preview: 1 } );
				preview = 1;
			}

			/**
			 * Set preview false to hide preview and show rendered form.
			 */
			function save_preview() {
				setFormHTML(null);
				props.setAttributes( { preview: 0 } );
				preview = 0;
			}

			/**
			 * Generate the ul li list associated lists element based on array of associated lists.
			 * 
			 * @param {*} index The index on the array of forms.
			 * @returns Ul li element of the associated lists of the form
			 */
			function form_lists( index ) {
				var lists = [];
				if ( forms.length > 0 && typeof forms[index].lists === 'object' && forms[index].lists !== null ) {
					var associated_lists = Object.values( forms[index].lists );
					associated_lists.forEach( element => {
						lists.push(
							el( 
								'li', 
								{ 
									style: { 'listStyleType': 'none' } 
								}, 
								element 
							)
						);
					});
				}
				return el( 'ul', {}, lists );
			}

			var children = [];
			
			if ( preview == 0 ) {
				children.push(
					el(
						BlockControls,
						{ key: 'controls' },
						el(
							ToolbarButton,
							{ 
								icon: 'edit',
								label: 'Edit',
								onClick:  show_preview
							}
						)
					)
				);
			} else {
				children.push(
					el(
						BlockControls,
						{ key: 'controls' },
						el(
							ToolbarButton,
							{ 
								icon: 'yes',
								label: 'Save',
								onClick: save_preview
							}
						)
					)
				);
			}

			if (first && 'block-' + props.clientId !== first) {
				setTimeout(() => {
					let nowFirst = getFirstFormId();
					if (!nowFirst || nowFirst === 'block-' + props.clientId) {
						setReRender(Date.now());
					}
				}, 200);
				children.push(
					el(
						'img',
						{
							id: 'icontact_block_logo',
							className: 'block-logo',
							src: icontact_forms_block_block_editor.logo_url
						}
					)
				);
				children.push ( el('div', { style: { 'textAlign': 'center' } }, 'You can only have one signup form per page.' ) );
				return el(
					'form',
					Object.assign( blockProps, { onSubmit: show_selected_form }, { autocomplete: 'off' } ),
					children
				);
			}
			if ( formid && preview == 0 ) {
				children.push( 
					el(renderForm,{ formHTML } )
				);
				return el(
					'div',
					Object.assign( blockProps, { 'data-active-form': 1, onSubmit: show_selected_form } ),
					children
				);
			}

			children.push(
				el(
					'img',
					 { 
						id: 'icontact_block_logo',
						className: 'block-logo',
						src: icontact_forms_block_block_editor.logo_url
					 }
				)
			);


			var $i = 0;
			var select_options = [];
			forms.forEach(element => {
				select_options.push(
					el( 'div',
						{ className: 'icform-input-tooltip' },
						[
							el( 
								'a', 
								{
								value: element.formid,
								cfid: element.cfid,
								cid: element.cid,
								index: $i,
								id: 'form-' + element.formid,
								href: '#' + element.name,
								onClick: show_selected_form
								}, 
								element.name 
							),
							el(
								'div',
								{ className: 'icform-input-tooltiptext' },
								[
									el('div',
										{ className: 'icform-tooltop-title'},
										el( 'span', {}, 'Associated Lists:')
									),
									form_lists( $i )
								]
							)
						]
					)
				);
				if ( formid == element.formid ) {
					current_index = $i;
				}
				$i++;
			});	

			if ( formid && forms.length > 0 ) {
				input_placeholder = forms[current_index].name;
			}

			children.push(
				el(	
					'input',
					{ 
						id: 'icform-input', 
						type: 'text', 
						onClick: show_list, 
						onKeyUp: filter_form_list, 
						placeholder: input_placeholder
					}
				),
				el(
					'div',
					{ id: 'icforms-list', className: 'hidden'  },
					select_options
				)
			);

			if ( formid ) {
				children.push(
					el(renderForm,{ formHTML })
				);
				children.push(
					el(
						'div',
						{ className: 'icform-preview-lists-info' },
						[
							el(
								'div',
								{ className: 'icform-preview-lists-title' },
								el( 'span', {}, 'Associated Lists:')
							),
							form_lists( current_index )
						]
					)
				);
			}

			return el(
				'form',
				Object.assign( blockProps, { onSubmit: show_selected_form }, {'data-active-form': 1, autocomplete: 'off' } ),
				children
			);
			
		},

		/**
		 * The save function defines the way in which the different attributes should be combined
		 * into the final markup, which is then serialized by Gutenberg into `post_content`.
		 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#save
		 *
		 * @return {Element}       Element to render.
		 */
		save: function( props ) {
			return null;
		}

	} );
});

