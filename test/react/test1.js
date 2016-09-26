var ListContain = React.createClass({
	render: function() {
		var rows = [];
		console.log("Run rows");
		rows.push(<CharSummary id="1" name="some name 1" />);
		rows.push(<CharSummary id="2" name="some name 2" />);
		return (
			<div>{rows}</div>
		)
	}

})

class CharSummary extends React.Component {
	constructor() {
    super();
    this.state = {
      liked: false
    };
    this.handleClick = this.handleClick.bind(this);
  }
	handleClick() {
		console.log("ADSFAFD");
		this.setState({liked: !this.state.liked});
	}
	render() {
		const text = this.state.liked ? 'liked' : 'haven\'t liked';
		return (
			<div onClick={this.handleClick} className="charSummaryContain">
				<div className="charSummaryName">{text}</div>
			</div>
		)
	}
}


var characterList = [
		{id:1, name:"name 1"},
		{id:2, name:"name 2"},
		];

ReactDOM.render(
	<ListContain characters={characterList} />,
	document.getElementById('example')
);
