const {
	i18n: { __ },
	components: { SelectControl },
	compose: { withState },
} = wp;

const LibraryAdd = withState( {
	set: '50%',
} )( ( { set, setState } ) => (
	<SelectControl
			label="Set"
			value={ set }
			options={ [
					{ label: 'Big', value: '100%' },
					{ label: 'Medium', value: '50%' },
					{ label: 'Small', value: '25%' },
			] }
			onChange={ ( set ) => { setState( { set } ) } }
	/>
) );

export default LibraryAdd;