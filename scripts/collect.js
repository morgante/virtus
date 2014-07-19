var fitbit = require('../lib/fitbit');
var db = require('../lib/db');

var Foods = require('../models/foods');
var Goals = require('../models/goals');

function collectActivites(date) {
	fitbit.client.getActivities({date: date}, function (err, activities) {
		if (err) {
			console.log(err);
			return;
		}

		console.log(activities);
	});
}

function collectFood(date) {
	fitbit.client.getFoods({date: date}, function (err, data) {
		if (err) {
			console.log(err);
			return;
		}

		var dateKey = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate();

		var foods = data._attributes.foods;
		var summary = data._attributes.summary;

		Foods.findOneAndUpdate({"date": dateKey}, {"foods": foods, "summary": summary}, {upsert: true}, function(err) {
			if (err) {
				console.log(err);
			}

			var methods = {
				d3: false
			};

			// assumes no fewer than 1000 calories per day
			if (summary.calories >= 1000) {
				methods.d3 = true;
			}

			Goals.findOneAndUpdate({"date": dateKey}, {"methods": methods}, {upsert: true}, function(err) {
				if (err) {
					console.log(err);
				}

				console.log('done');
			});
		});
	});
}

var date = new Date('2014-07-19');

collectFood(date);