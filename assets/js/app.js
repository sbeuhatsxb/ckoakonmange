// assets/js/app.js
require('../css/app.min.css');
require('../css/bootstrap.min.css');
// require('../img/tasteofparis.jpg');
require('../css/fontawesome/css/font-awesome.min.css');
// loads the jquery package from node_modules
const $ = require('jquery');

// import the function from greet.js (the .js extension is optional)
// ./ (or ../) means to look for a local file
var greet = require('./greet');

$(document).ready(function() {
    $('body').prepend('<h1>'+greet('john')+'</h1>');
});

const imagesContext = require.context('../img', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);