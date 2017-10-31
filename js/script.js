
const pusher = new Pusher($('meta[name="_key"]').attr('content'), {
    cluster: $('meta[name="_cluster"]').attr('content'),
    encrypted: true
});

const channel = pusher.subscribe('my-channel');

channel.bind('chat-event', function(data) {
    let squirl = vm.squirlz[data.id];
    messages.unshift({
        image: squirl.image,
        name: squirl.name,
        message: data.message,
        time: data.time
    });
});

channel.bind('select-event', function(data) {
    let items = data.acorns;
    $.each(items, function(k,v){
        acorns[k].active = parseInt(v.active);
    });
});

let messages = [];

let acorns = [];

for (i = 1; i <= 25; i++) {
    let acorn = {
        number: i, 
        active: 1
    };
    acorns.push(acorn);
}

let vm = new Vue({

    el: '#wrapper',
    data: {
        messages: messages,
        acorns: acorns,
        mysquirl: '',
        messageSent: false,
        squirlz: [{
            name: 'Ralph',
            image: 'images/characters/ralph.png',
            class: 'col-2 offset-1',
            locked: false
        },{
            name: 'Federico',
            image: 'images/characters/federico.png',
            class: 'col-2',
            locked: false
        },{
            name: 'Bianca',
            image: 'images/characters/bianca.png',
            class: 'col-2',
            locked: false
        },{
            name: 'Jay',
            image: 'images/characters/jay.png',
            class: 'col-2',
            locked: false
        },{
            name: 'Tubz',
            image: 'images/characters/tubz.png',
            class: 'col-2',
            locked: false
        }]
    },
    methods: {
        selectSquirl: function(index, squirl) {
            if(this.messageSent === false) {
                squirl.id = index;
                this.mysquirl = squirl;
            }
        }
    }

});

function toggle(n){
    const key = n.number - 1;
    const items = acorns;
    items[key].active = n.active == 1 ? 0 : 1;
    $.ajax({
        url: "pusher.php",
        type: "POST",
        data: {
            type: 'select',
            data: items
        }
    });
}

function addMessage() {
    $("#messageForm").validate({
        rules: {
            name: {
                required: true
            },
            message: {
                required: true
            }
        },
        messages: {
            name: {
                required: "select a squirl"
            },
            message: {
                required: "message required"
            }
        },
        submitHandler: function(form) {
            $.ajax({
                url: "pusher.php",
                type: "POST",
                data: {
                    type: 'chat',
                    csrf: $('meta[name="_token"]').attr('content'),
                    id: $('#id').val(),
                    message: $('#message').val()
                },
                success: function() {
                    $('#message').val("");
                    vm.messageSent = true;
                    $.each(vm.squirlz, function(index, squirl) {
                        if(squirl.name === vm.mysquirl) squirl.locked = true;
                    });
                }
            });
        }
    });
}
