var Goals = require('../models/goals');

module.exports = {
	"list": function(req, res, next) {
		Goals.find({}, function(err, goals) {
			if (err) {
				res.send(err);
				console.log(err);
				return;
			}

			res.send(goals);
		});
	}
};