// we need mongoose
var mongoose = require('mongoose');
var _ = require('../public/lib/underscore');
	
var schema = mongoose.Schema({
	"date": String,
	"methods": mongoose.Schema.Types.Mixed
});

var Goals = module.exports = mongoose.model('Goals', schema);