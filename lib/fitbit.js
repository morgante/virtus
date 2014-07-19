var Fitbit = require('fitbit');

var client = new Fitbit(process.env.FITBIT_CONSUMER_KEY, process.env.FITBIT_CONSUMER_SECRET, {
	accessToken: process.env.FITBIT_USER_TOKEN,
	accessTokenSecret: process.env.FITBIT_USER_SECRET,
	unitMeasure: 'en_US'
});

module.exports = {
	call: client.apiCall,
	client: client
};