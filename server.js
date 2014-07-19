var express = require('express')
		, http = require('http')
		, url = require('url')
		, async = require('async')
		, request = require('request')
		, mongoose = require('mongoose')
		, _ = require('./public/lib/underscore')
		
var pkg = require('./package.json')
		, main = require('./routes/main')

var Fitbit = require('fitbit');
var fitbit = new Fitbit(process.env.FITBIT_CONSUMER_KEY, process.env.FITBIT_CONSUMER_SECRET);

var passport = require('passport');
var FitbitStrategy = require('passport-fitbit').Strategy;

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
app.get('/', main.index);

passport.use(new FitbitStrategy({
	consumerKey: process.env.FITBIT_CONSUMER_KEY,
	consumerSecret: process.env.FITBIT_CONSUMER_SECRET,
	callbackURL: "http://localhost:" + process.env.PORT + "/auth/fitbit/callback"
}, function(token, tokenSecret, profile, done) {
	console.log(profile, token, tokenSecret);
	// done();
    // User.findOrCreate({ fitbitId: profile.id }, function (err, user) {
    //   return done(err, user);
    // });
  }
));

// fitbit auth
app.get('/auth/fitbit', passport.authenticate('fitbit'));

app.get('/auth/fitbit/callback', passport.authenticate('fitbit', { failureRedirect: '/login' }),
	function(req, res) {
		// Successful authentication, redirect home.
		res.redirect('/');
	});

// start listening
app.listen( process.env.PORT , function() {
  console.log('Express server listening on port ' + process.env.PORT);
});