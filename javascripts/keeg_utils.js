Function.prototype.p = function () {
    // capture the bound arguments
    var args = Array.prototype.slice.call(arguments);
    var f = this;
    // construct a new function
    return function () {
        // prepend argument list with the closed arguments from above
        var inner_args = Array.prototype.slice.call(arguments);
        return f.apply(this, args.concat(inner_args))
    };
};
var ch = function compose(f) {
    var queue = f ? [f] : [];
    var fn = function fn(g) {
        if (arguments.length) {
            queue.push(g);
            return fn;
        }
        return function() {
            var args = Array.prototype.slice.call(arguments);
            queue.forEach(function(func) {
                args = [func.apply(this, args)];
            });
            return args[0];
        }
    };
    return fn;
};

/*Function.prototype.c = function (g) {
    // preserve f
    var f = this;
    // construct function z
    return function () {
        var args = Array.prototype.slice.call(arguments);
        // when called, nest g's return in a call to f
        return f.call(this, g.apply(this, args));
    };
};

Function.prototype.f = function () {
    // preserve f
    var f = this;
    // construct g
    return function () {
        var args = Array.prototype.slice.call(arguments);
        // flip arguments when called
        return f.apply(this, args.reverse());
    };
};
*/
var op = {
    "+": function (x, y) x + y,
    "==": function (x, y) x == y,
    "===": function (x, y) x === y,
    "!": function (x)!x
};/*

Array.prototype.zip = function (l) {
    var result = new Array();
    for (var x = 0; x < smaller(this.length, l.length); x++) {
        result.push([this[x], l[x]]);
    }
    return result;
};

Array.prototype.zipWith = function (f, a) {
    var result = new Array();
    for (var x = 0; x < smaller(this.length, a.length); x++) {
        result.push(f(this[x], a[x]));
    }
    return result;
};

Array.prototype.first = function () {
    if (this.length > 0) {
        return this[0];
    } else {
        throw "Array Empty!";
    }
};

//True if all values in array are identical
Array.prototype.same = function () this.reduce(op["=="], true);

//True if two arrays are identical
Array.prototype.identicalTo = function (a) {
    if (this.length != a.length) return false;
    return this.zipWith(op["=="], a).same()
};*/

var smaller = function (a, b)(a < b) ? a : b;

function randomIntFromInterval(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}

