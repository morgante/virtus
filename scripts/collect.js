var fitbit = require('../lib/fitbit');

var date = new Date();

function collectActivites() {
	fitbit.client.getActivities({date: date}, function (err, activities) {
		if (err) {
			console.log(err);
			return;
		}

		console.log(activities);
	});
}