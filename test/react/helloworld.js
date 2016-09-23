
var SecondBox = React.createClass({displayName: 'secondBox', render: function() {
	console.log("make container");
	return (
		<div className="smallBox"><h2 className="anotherClass">{this.props.content}</h2>{this.props.children}</div>
	);
}
});

var FirstBox = React.createClass({displayName: 'CommentBox', 
	render: function () {
		console.log("render something");
		return (
			<div className="commentBox">
				<SecondBox content="ox1">title</SecondBox>
				<SecondBox content="ox2">title 2</SecondBox>
			</div>
			);
	}
});

class SomeButton extends React.Component {
	constructor() {
		super();
		this.state = {
			selected:false
		}
		this.handleClick = this.handleClick.bind(this);
	}
	
	handleClick() {
		this.setState({selected: !this.state.selected});
	}
	
	render() {
    const text = this.state.selected ? 'liked' : 'haven\'t liked';
    return (
      <div className="secondContain" onClick={this.handleClick}>
        You {text} this. Click to toggle.
      </div>
    );
  }
}


//ReactDOM.render(React.createElement(firstBox, null), document.getElementById('example'));
//ReactDOM.render(React.createElement(SecondBox, null), document.getElementById('example'));
ReactDOM.render(<FirstBox />, document.getElementById('example'));
ReactDOM.render( <SomeButton />,  document.getElementById('example2'));
console.log("done");