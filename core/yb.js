
function sleeptime(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

temp1.forEach(function(item, index) {doInvestBoxInvestClose($(item[5]).attr('ipid');, 0);});

function setInvest(id, amount){
    
}

// Закроет все инвесты по очереди с интервалом 800 мс
function doInvestBoxInvestClose(pack_id, confirm) {
    var csrf_token = $('#csrf_token').val();
    $.ajax({
        url: '/ajax/system_investbox.php',
        cache: !1,
        type: 'POST',
        data: {
            action: 'invest_close',
            pack_id: pack_id,
            confirm: confirm,
            csrf_token: csrf_token
        },
        dataType: 'json',
        success: function(data) {
            if (data.result == "OK") {
                console.log("OK: " + pack_id + " csrf " + csrf_token)
            } else if (data.error == 2) {
                new Messi(data.error_log,{
                    title: popup_title_waring,
                    titleClass: 'info',
                    buttons: [{
                        id: 0,
                        label: popup_btn_close,
                        val: 'Yes',
                        "class": 'btn-danger'
                    }, {
                        id: 1,
                        label: popup_btn_cancel,
                        val: 'No'
                    }],
                    modal: true,
                    callback: function(val) {
                        if (val == 'Yes') {
                            doInvestBoxInvestClose(pack_id, 1);
                        }
                    }
                });
            } else {
                new Messi(data.error_log,{
                    title: popup_title_error,
                    titleClass: 'error',
                    buttons: [{
                        id: 0,
                        label: popup_btn_close,
                        val: 'Close'
                    }],
                    modal: true
                });
            }
        },
        error: function() {}
    });
}

var intervalId = setInterval(function(){
    if(ib.length)
        doInvestBoxInvestClose_ib(ib.pop(), 0);
    else
        clearInterval(intervalId);
}, 800);