// we need mongoose
var mongoose = require('mongoose');
var _ = require('../public/lib/underscore');
	
var schema = mongoose.Schema({
	"date": String,
	"foods": mongoose.Schema.Types.Mixed,
	"summary": mongoose.Schema.Types.Mixed
});

var Foods = module.exports = mongoose.model('Foods', schema);