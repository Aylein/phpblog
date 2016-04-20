var fs = require('fs'), gm = require('gm');

for(var i = 1; i < 51; i++){
    gm("./source/ac/ac_" + i + ".png").crop(69, 69, 0, 0).write("./release/images/ac/ac_" + i + ".png", function(err){
        if(err) console.log(err);
    });
}

console.log("done");