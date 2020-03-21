const {
	i18n: { __ },
	element: { Component, createRef }
} = wp;

class LibraryAdd extends Component {
	idRef = createRef();

	state = {
		payload: {
			cards: {
				"ssh-21": 1,
        "ssh-77": 1,
				"ssh-98": 1,
        "ssh-13": 1,
        "ssh-70": 1,
        "ssh-27": 1,
        "ssh-11": 1,
        "ssh-83": 1,
        "ssh-33": 2,
        "ssh-10": 1
    	}
		}
	};

	addToPayload = (card) => {
    // 1. Take a copy of the existing state
    const payload = { ...this.state.payload };
    // 2. Add our new fish to that fishes variable
    payload.cards[card.id] = card.quantity;
    // 3. Set the new fishes object to state
    this.setState({ payload });
	};
	
	render() {
		return (
			<ul className="fishes">
				{Object.keys(this.state.payload.cards).map(key => (
					<li>{key}x{this.state.payload.cards[key]}</li>
				))}
			</ul>
		);
	}
}

export default LibraryAdd;