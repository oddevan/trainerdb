const { render } = wp.element;

import LibraryAdd from './Components/LibraryAdd';

render(
	(<LibraryAdd />),
	document.querySelector('div#tdb-library-add-react')
);