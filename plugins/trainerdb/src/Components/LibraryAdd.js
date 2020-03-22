const {
	i18n: { __ },
	components: { Button, TextControl },
	compose: { withState },
	element: { Component },
} = wp;

class LibraryAdd extends Component {

	state = {
		payload: {
			cards: {}
		},
		scratch: ''
	};

	addToPayload = (newId) => {
    // 1. Take a copy of the existing state
    const payload = { ...this.state.payload };
		// 2. Add our new fish to that fishes variable
		if (payload.cards[newId]) {
			payload.cards[newId] += 1;
		} else {
			payload.cards[newId] = 1;
		}
    // 3. Set the new fishes object to state
    this.setState({ payload });
	};

	updateScratch = (newId) => {
		this.setState({ scratch: newId });
	}
	
	render() {
		return (
			<div>
				<ul className="fishes">
					{Object.keys(this.state.payload.cards).map(key => (
						<li>{key} &times; {this.state.payload.cards[key]}</li>
					))}
				</ul>
				<TextControl
					label={__('Card ID', 'trainerdb')}
					value={this.state.scratch}
					onChange={this.updateScratch}
				/>
				<Button
					onClick={() => {this.addToPayload(this.state.scratch)}}
					isDefault
				>
					{__('Add Card', 'trainerdb')}
				</Button>
				<Button
					onClick={() => {console.log(this.state.payload)}}
					isPrimary
				>
					{__('Add Cards to Library', 'trainerdb')}
				</Button>
			</div>
		);
	}
}

export default LibraryAdd;