var express = require('express')
		, http = require('http')
		, url = require('url')
		, async = require('async')
		, request = require('request')
		, mongoose = require('mongoose')
		, _ = require('./public/lib/underscore')

var fitbit = require('fitbit-js')(process.env.FITBIT_CONSUMER_KEY, process.env.FITBIT_CONSUMER_SECRET, 'http://localhost:' + process.env.PORT);
		
var pkg = require('./package.json')
		, main = require('./routes/main')

// set up Mongoose
mongoose.connect('localhost', pkg.name);
var db = mongoose.connection;
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback() {
  console.log('Connected to DB');
});

var app = express();
// configure Express
app.configure(function() {
	app.set('views', __dirname + '/views');
	app.set('view engine', 'ejs');
	app.engine('ejs', require('ejs-locals'));

	app.use(express.logger());
	app.use(express.cookieParser());
	app.use(express.bodyParser());
	app.use(express.methodOverride());
	app.use(express.session({ secret: process.env.SECRET }));
	app.use(app.router);
	app.use(express.static(__dirname + '/public'));
});

// set up routes
// app.get('/', main.index);

app.get('/', function(req, res) {
	fitbit.getAccessToken(req, res, function(err, token) {
		console.log(err, token);
		if (token) {
			res.send(token);
		}
	});
});

// start listening
app.listen( process.env.PORT , function() {
  console.log('Express server listening on port ' + process.env.PORT);
});