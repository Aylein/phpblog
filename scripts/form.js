var regex = {
    isNumber: /^\-?\d+$/,
    isEmail: /^[\w\-_\.]+@[\w]+(\.[a-z\d]+)+$/,
    isMobile: /^[1][3|5|7|8][\d]{9}$/,
    isTelephone: /^(\d{3,4}[\-|\s])?\d{7,8}$/,
    isChinese: /^[\u4E00-\u9FA5]+$/,
    _byte: /[^\x00-\xff]/ig,
    blank: /\s+/
};

!function(){
    Unable = {
        isLock: function(q){ return q.attr("lock"); },
        lock: function(q){
            if(!Unable.isLock(q)){
                q.attr("disabled", "disabled").attr("lock", "lock").css("opacity", "0.4");
            }
        },
        unLock: function(q){
            if(Unable.isLock(q)){
                q.removeAttr("disabled", "disabled").removeAttr("lock", "lock").css("opacity", "1.0");
            }
        }
    };
}();