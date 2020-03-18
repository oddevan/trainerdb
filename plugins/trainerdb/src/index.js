const { render } = wp.element;

console.log('Boom!');

render(
	(<p>This is a drill, this is a drill.</p>),
	document.querySelector('div#tdb-library-add-react')
);